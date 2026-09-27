<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_published_posts_across_categories_without_overwriting_edits(): void
    {
        $this->seed(PostSeeder::class);

        $this->assertSame(6, Post::published()->count());
        $this->assertSame(6, Post::distinct()->count('category'));
        $this->assertSame(1, Post::where('is_featured', true)->count());

        $post = Post::where('slug', 'shipping-llm-features-in-laravel')->first();
        $post->update(['title' => 'Edited in admin']);

        $this->seed(PostSeeder::class);

        $this->assertSame(6, Post::count());
        $this->assertSame('Edited in admin', $post->fresh()->title);
    }

    public function test_index_shows_featured_post_categories_and_grid(): void
    {
        $this->seed(PostSeeder::class);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Featured')
            ->assertSee('Shipping LLM Features in Laravel Without the Surprises')
            ->assertSee('Laravel Queue Jobs That Survive Production')
            ->assertSee(route('blog.index', ['category' => 'Shopify']), false);
    }

    public function test_index_filters_by_category_search_and_tag(): void
    {
        $this->seed(PostSeeder::class);

        $this->get(route('blog.index', ['category' => 'Shopify']))
            ->assertOk()
            ->assertSee('Shopify Webhooks in Laravel')
            ->assertDontSee('Finding and Fixing N+1 Queries in Eloquent');

        $this->get(route('blog.index', ['q' => 'preventLazyLoading']))
            ->assertOk()
            ->assertSee('Finding and Fixing N+1 Queries in Eloquent')
            ->assertDontSee('Thin Controllers in Laravel');

        $this->get(route('blog.index', ['tag' => 'horizon']))
            ->assertOk()
            ->assertSee('Laravel Queue Jobs That Survive Production')
            ->assertDontSee('Using AI Coding Assistants');

        $this->get(route('blog.index', ['q' => 'no-such-thing-anywhere']))
            ->assertOk()
            ->assertSee('No articles found');
    }

    public function test_article_page_renders_toc_tags_and_navigation(): void
    {
        $this->seed(PostSeeder::class);

        $this->get(route('blog.show', 'laravel-queue-jobs-that-survive-production'))
            ->assertOk()
            ->assertSee('On this page')
            ->assertSee('id="assume-every-job-will-run-more-than-once"', false)
            ->assertSee('href="#assume-every-job-will-run-more-than-once"', false)
            ->assertSee('#horizon')
            ->assertSee('Keep reading')
            ->assertSee('Previous')
            ->assertSee('Next');
    }

    public function test_unpublished_posts_are_hidden(): void
    {
        Post::create([
            'title' => 'Secret draft',
            'slug' => 'secret-draft',
            'content' => '<p>Draft</p>',
            'is_published' => false,
        ]);

        $this->get(route('blog.show', 'secret-draft'))->assertNotFound();
        $this->get(route('blog.index'))->assertDontSee('Secret draft');
    }

    public function test_admin_can_save_category_tags_and_featured_flag(): void
    {
        $admin = User::factory()->create(['email' => config('app.admin_email')]);

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Hello Blog',
                'category' => 'Laravel',
                'tags' => 'queues, Queues , redis,,',
                'content' => '<h2>Intro</h2><p>Body</p>',
                'is_published' => '1',
                'is_featured' => '1',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::where('slug', 'hello-blog')->firstOrFail();

        $this->assertSame('Laravel', $post->category);
        $this->assertSame(['queues', 'redis'], $post->tags);
        $this->assertTrue($post->is_featured);
        $this->assertNotNull($post->published_at);
    }
}
