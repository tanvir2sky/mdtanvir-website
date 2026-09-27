<?php

namespace Tests\Feature;

use App\Models\GuestbookEntry;
use App\Models\User;
use App\Notifications\NewGuestbookEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GuestbookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret_key' => null]);
        Notification::fake();
    }

    private function admin(): User
    {
        return User::factory()->create(['email' => config('app.admin_email')]);
    }

    public function test_notes_are_pending_until_approved(): void
    {
        $this->post(route('guestbook.store'), ['name' => 'Ada', 'message' => 'Love the cron tool!', 'website' => 'https://ada.dev'])
            ->assertRedirect(route('guestbook.index').'#sign')
            ->assertSessionHas('guestbook_status');

        $entry = GuestbookEntry::firstOrFail();
        $this->assertNull($entry->approved_at);
        Notification::assertSentOnDemand(NewGuestbookEntry::class);

        $this->get(route('guestbook.index'))->assertOk()->assertDontSee('Love the cron tool!')->assertSee('No notes yet');

        $this->actingAs($this->admin())->patch(route('admin.guestbook.approve', $entry))->assertRedirect();

        $this->get(route('guestbook.index'))
            ->assertSee('Love the cron tool!')
            ->assertSee('rel="nofollow ugc noopener noreferrer"', false);
    }

    public function test_messages_are_escaped(): void
    {
        GuestbookEntry::create(['name' => '<b>Eve</b>', 'message' => '<script>alert(1)</script>', 'visitor_hash' => 'x', 'approved_at' => now()]);

        $this->get(route('guestbook.index'))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_validation_and_honeypot(): void
    {
        $this->post(route('guestbook.store'), ['name' => '', 'message' => 'x', 'website' => 'javascript:alert(1)'])
            ->assertSessionHasErrorsIn('guestbook', ['name', 'message', 'website']);

        $this->post(route('guestbook.store'), ['name' => 'Bot', 'message' => 'Buy now', 'company' => 'Spam Inc'])
            ->assertSessionHas('guestbook_status');

        $this->assertSame(0, GuestbookEntry::count());
        Notification::assertNothingSent();
    }

    public function test_german_submission_redirects_to_german_page(): void
    {
        $this->post(route('guestbook.store'), ['name' => 'Jonas', 'message' => 'Tolle Seite!', 'locale' => 'de'])
            ->assertRedirect(route('de.guestbook.index').'#sign')
            ->assertSessionHas('guestbook_status', 'Danke für deinen Eintrag! Er erscheint nach einer kurzen Prüfung.');

        $this->assertSame('de', GuestbookEntry::first()->locale);
    }

    public function test_admin_can_filter_hide_and_delete(): void
    {
        $admin = $this->admin();
        $approved = GuestbookEntry::create(['name' => 'A', 'message' => 'Approved note', 'visitor_hash' => 'x', 'approved_at' => now()]);
        $pending = GuestbookEntry::create(['name' => 'B', 'message' => 'Pending note', 'visitor_hash' => 'y']);

        $this->actingAs($admin)->get(route('admin.guestbook.index', ['status' => 'pending']))
            ->assertOk()->assertSee('Pending note')->assertDontSee('Approved note');

        $this->actingAs($admin)->patch(route('admin.guestbook.unapprove', $approved));
        $this->assertNull($approved->fresh()->approved_at);

        $this->actingAs($admin)->delete(route('admin.guestbook.destroy', $pending));
        $this->assertSame(1, GuestbookEntry::count());
    }
}
