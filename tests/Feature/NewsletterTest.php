<?php

namespace Tests\Feature;

use App\Jobs\SendPostToSubscribers;
use App\Mail\ConfirmSubscription;
use App\Mail\NewPostPublished;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['email' => config('app.admin_email')]);
    }

    private function subscriber(array $attributes = []): Subscriber
    {
        return Subscriber::create($attributes + [
            'email' => 'reader@example.com',
            'locale' => 'en',
            'confirmed_at' => now(),
        ]);
    }

    public function test_subscribing_sends_a_confirmation_email(): void
    {
        Mail::fake();

        $this->from(route('blog.index'))
            ->post(route('newsletter.store'), ['email' => 'Reader@Example.com', 'locale' => 'de', 'source' => 'blog-index'])
            ->assertRedirect(route('blog.index').'#newsletter')
            ->assertSessionHas('newsletter_status', 'Fast geschafft! Bitte bestätige deine Anmeldung über den Link in deinem Postfach.');

        $subscriber = Subscriber::firstOrFail();
        $this->assertSame('reader@example.com', $subscriber->email);
        $this->assertSame('de', $subscriber->locale);
        $this->assertSame('blog-index', $subscriber->source);
        $this->assertNull($subscriber->confirmed_at);

        Mail::assertQueued(ConfirmSubscription::class, fn ($mail) => $mail->hasTo('reader@example.com') && $mail->locale === 'de');
    }

    public function test_invalid_email_is_rejected(): void
    {
        Mail::fake();

        $this->post(route('newsletter.store'), ['email' => 'nope'])
            ->assertSessionHasErrorsIn('newsletter', 'email');

        $this->assertSame(0, Subscriber::count());
        Mail::assertNothingQueued();
    }

    public function test_honeypot_blocks_bots_silently(): void
    {
        Mail::fake();

        $this->post(route('newsletter.store'), ['email' => 'bot@example.com', 'website' => 'http://spam.test'])
            ->assertSessionHas('newsletter_status');

        $this->assertSame(0, Subscriber::count());
        Mail::assertNothingQueued();
    }

    public function test_existing_active_subscriber_gets_same_message_and_no_email(): void
    {
        Mail::fake();
        $this->subscriber();

        $this->post(route('newsletter.store'), ['email' => 'reader@example.com'])
            ->assertSessionHas('newsletter_status');

        $this->assertSame(1, Subscriber::count());
        Mail::assertNothingQueued();
    }

    public function test_confirmation_link_confirms_and_expires(): void
    {
        $subscriber = $this->subscriber(['confirmed_at' => null, 'locale' => 'de']);

        $this->get($subscriber->confirmUrl())->assertRedirect(route('de.newsletter.confirmed'));
        $this->assertNotNull($subscriber->fresh()->confirmed_at);

        $this->get(route('de.newsletter.confirmed'))->assertOk()->assertSee('Du bist angemeldet!');

        $other = $this->subscriber(['email' => 'late@example.com', 'confirmed_at' => null]);
        $expired = URL::temporarySignedRoute('newsletter.confirm', now()->subMinute(), ['subscriber' => $other->id]);
        $this->get($expired)->assertForbidden();
        $this->get(route('newsletter.confirm', $other))->assertForbidden();
        $this->assertNull($other->fresh()->confirmed_at);
    }

    public function test_unsubscribe_link_and_one_click_post(): void
    {
        $subscriber = $this->subscriber();

        $this->get($subscriber->unsubscribeUrl())->assertRedirect(route('newsletter.unsubscribed'));
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);

        $other = $this->subscriber(['email' => 'other@example.com']);
        $this->post($other->unsubscribeUrl())->assertNoContent();
        $this->assertNotNull($other->fresh()->unsubscribed_at);

        $this->get(route('newsletter.unsubscribe', 'wrong-token'))->assertNotFound();
    }

    public function test_publishing_a_post_emails_only_active_subscribers_once(): void
    {
        Mail::fake();
        $this->subscriber(['email' => 'active@example.com']);
        $this->subscriber(['email' => 'pending@example.com', 'confirmed_at' => null]);
        $this->subscriber(['email' => 'gone@example.com', 'unsubscribed_at' => now()]);

        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Fresh article',
            'content' => '<p>Hello</p>',
            'is_published' => '1',
            'notify_subscribers' => '1',
        ]);

        $post = Post::where('slug', 'fresh-article')->firstOrFail();
        $this->assertNotNull($post->newsletter_sent_at);

        Mail::assertQueued(NewPostPublished::class, 1);
        Mail::assertQueued(NewPostPublished::class, fn ($mail) => $mail->hasTo('active@example.com'));

        // Saving again never re-sends.
        $this->actingAs($admin)->put(route('admin.posts.update', $post), [
            'title' => 'Fresh article',
            'content' => '<p>Hello again</p>',
            'is_published' => '1',
            'notify_subscribers' => '1',
        ]);
        (new SendPostToSubscribers($post))->handle();

        Mail::assertQueued(NewPostPublished::class, 1);
    }

    public function test_posts_can_opt_out_and_drafts_are_not_sent(): void
    {
        Queue::fake();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Quiet post', 'content' => '<p>x</p>', 'is_published' => '1',
        ]);
        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Draft post', 'content' => '<p>x</p>', 'notify_subscribers' => '1',
        ]);

        Queue::assertNotPushed(SendPostToSubscribers::class);
    }

    public function test_scheduled_posts_are_sent_by_the_command_when_due(): void
    {
        Queue::fake();

        $this->actingAs($this->admin())->post(route('admin.posts.store'), [
            'title' => 'Future post',
            'content' => '<p>x</p>',
            'is_published' => '1',
            'notify_subscribers' => '1',
            'published_at' => now()->addHour()->format('Y-m-d H:i'),
        ]);

        Queue::assertNotPushed(SendPostToSubscribers::class);

        $this->artisan('newsletter:send-due')->assertSuccessful();
        Queue::assertNotPushed(SendPostToSubscribers::class);

        $this->travel(2)->hours();
        $this->artisan('newsletter:send-due')->assertSuccessful();
        Queue::assertPushed(SendPostToSubscribers::class, 1);
    }

    public function test_new_post_email_contains_unsubscribe_link_and_header(): void
    {
        $subscriber = $this->subscriber();
        $post = Post::create(['title' => 'Mail test', 'slug' => 'mail-test', 'content' => '<p>Body</p>', 'is_published' => true, 'published_at' => now()]);

        $mail = new NewPostPublished($post, $subscriber);

        $mail->assertSeeInHtml('Mail test');
        $mail->assertSeeInHtml($subscriber->unsubscribeUrl());
        $this->assertSame('<'.$subscriber->unsubscribeUrl().'>', $mail->headers()->text['List-Unsubscribe']);
    }

    public function test_admin_can_list_filter_export_and_delete_subscribers(): void
    {
        $admin = $this->admin();
        $this->subscriber(['email' => 'active@example.com']);
        $pending = $this->subscriber(['email' => 'pending@example.com', 'confirmed_at' => null]);

        $this->actingAs($admin)->get(route('admin.subscribers.index'))
            ->assertOk()->assertSee('active@example.com')->assertSee('pending@example.com');

        $this->actingAs($admin)->get(route('admin.subscribers.index', ['status' => 'pending']))
            ->assertOk()->assertSee('pending@example.com')->assertDontSee('active@example.com');

        $csv = $this->actingAs($admin)->get(route('admin.subscribers.export'))->assertOk()->streamedContent();
        $this->assertStringContainsString('active@example.com', $csv);
        $this->assertStringNotContainsString('pending@example.com', $csv);

        $this->actingAs($admin)->delete(route('admin.subscribers.destroy', $pending))->assertRedirect();
        $this->assertSame(1, Subscriber::count());
    }

    public function test_signup_form_appears_on_blog_pages(): void
    {
        $this->get(route('blog.index'))->assertSee('id="newsletter"', false)->assertSee(route('newsletter.store'), false);
        $this->get(route('de.blog.index'))->assertSee('Neue Artikel direkt in dein Postfach');
    }
}
