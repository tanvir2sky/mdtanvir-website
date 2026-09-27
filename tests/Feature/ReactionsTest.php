<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReactionsTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $attributes = []): Post
    {
        return Post::create($attributes + [
            'title' => 'Queues in practice',
            'slug' => 'queues-in-practice',
            'content' => '<p>Body</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);
    }

    public function test_reactions_toggle_per_visitor(): void
    {
        $post = $this->makePost();

        $this->postJson(route('blog.react', $post->slug), ['type' => 'fire'])
            ->assertOk()
            ->assertJson(['counts' => ['like' => 0, 'fire' => 1, 'idea' => 0], 'mine' => ['fire']]);

        // A different visitor adds to the count.
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->postJson(route('blog.react', $post->slug), ['type' => 'fire'])
            ->assertJson(['counts' => ['fire' => 2]]);

        // Toggling again removes only this visitor's reaction.
        $this->postJson(route('blog.react', $post->slug), ['type' => 'fire'])
            ->assertJson(['counts' => ['fire' => 1], 'mine' => []]);

        $this->assertSame(1, $post->reactions()->count());
    }

    public function test_invalid_reactions_are_rejected(): void
    {
        $post = $this->makePost();
        $draft = $this->makePost(['slug' => 'draft', 'is_published' => false]);

        $this->postJson(route('blog.react', $post->slug), ['type' => 'angry'])->assertUnprocessable();
        $this->postJson(route('blog.react', $draft->slug), ['type' => 'like'])->assertNotFound();
        $this->postJson(route('blog.react', 'missing'), ['type' => 'like'])->assertNotFound();
    }

    public function test_article_shows_reaction_bar_with_state(): void
    {
        $post = $this->makePost();
        $this->postJson(route('blog.react', $post->slug), ['type' => 'idea']);

        $html = $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Was this article useful?')
            ->getContent();

        $this->assertMatchesRegularExpression('/data-reaction="idea"\s+aria-pressed="true"/', $html);
        $this->assertMatchesRegularExpression('/data-reaction="like"\s+aria-pressed="false"/', $html);
    }

    public function test_views_are_counted_once_per_visitor_and_skip_bots_and_admins(): void
    {
        $post = $this->makePost();
        $updatedAt = $post->updated_at;

        $this->get(route('blog.show', $post->slug));
        $this->get(route('blog.show', $post->slug));
        $this->assertSame(1, $post->fresh()->views_count);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])->get(route('blog.show', $post->slug));
        $this->assertSame(2, $post->fresh()->views_count);

        $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1)')
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.11'])
            ->get(route('blog.show', $post->slug));
        $this->assertSame(2, $post->fresh()->views_count);

        $this->actingAs(User::factory()->create(['email' => config('app.admin_email')]))
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.12'])
            ->get(route('blog.show', $post->slug));
        $this->assertSame(2, $post->fresh()->views_count);

        $this->assertEquals($updatedAt, $post->fresh()->updated_at, 'Views must not touch updated_at');
    }

    public function test_popular_sort_orders_by_views(): void
    {
        $this->makePost(['title' => 'Quiet post', 'slug' => 'quiet', 'published_at' => now()->subHour()]);
        $loud = $this->makePost(['title' => 'Loud post', 'slug' => 'loud', 'published_at' => now()->subDays(3)]);
        Post::whereKey($loud->id)->update(['views_count' => 1500]);

        $latest = $this->get(route('blog.index'))->getContent();
        $this->assertLessThan(strpos($latest, 'Loud post'), strpos($latest, 'Quiet post'));

        $popular = $this->get(route('blog.index', ['sort' => 'popular']))
            ->assertSee('1.5k')
            ->getContent();
        $this->assertLessThan(strpos($popular, 'Quiet post'), strpos($popular, 'Loud post'));
    }

    public function test_admin_sees_views_and_reactions(): void
    {
        $post = $this->makePost();
        Post::whereKey($post->id)->update(['views_count' => 77]);
        $this->postJson(route('blog.react', $post->slug), ['type' => 'like']);

        $admin = User::factory()->create(['email' => config('app.admin_email')]);

        $this->actingAs($admin)->get(route('admin.posts.index'))->assertOk()->assertSee('77');
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Top Posts')->assertSee('Queues in practice');
    }
}
