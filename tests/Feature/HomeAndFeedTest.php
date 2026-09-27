<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeAndFeedTest extends TestCase
{
    use RefreshDatabase;

    private function publishedPost(array $attributes = []): Post
    {
        return Post::create(array_merge([
            'title' => 'Scaling Laravel queues',
            'slug' => 'scaling-laravel-queues',
            'excerpt' => 'Lessons learned.',
            'content' => str_repeat('word ', 450),
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $attributes));
    }

    public function test_home_page_renders_ai_content_terminal_and_latest_posts(): void
    {
        $this->publishedPost();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('AI & LLM', false)
            ->assertSee('hero-terminal', false)
            ->assertSee('project-brief', false)
            ->assertSee('Previously')
            ->assertSee('Altruan GmbH')
            ->assertSee('2020 - 2025')
            ->assertSee('Scaling Laravel queues')
            ->assertSee('3 min read');
    }

    public function test_home_page_hides_blog_strip_without_posts(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Latest writing');
    }

    public function test_rss_feed_lists_published_posts(): void
    {
        $this->publishedPost();
        $this->publishedPost(['title' => 'Draft', 'slug' => 'draft', 'is_published' => false]);

        $response = $this->get(route('feed'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('Scaling Laravel queues')
            ->assertDontSee('<title>Draft</title>', false);

        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }

    public function test_sitemap_lists_home_blog_and_posts(): void
    {
        $this->publishedPost();

        $response = $this->get(route('sitemap'));

        $response->assertOk()
            ->assertSee(route('blog.show', 'scaling-laravel-queues'), false);

        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }
}
