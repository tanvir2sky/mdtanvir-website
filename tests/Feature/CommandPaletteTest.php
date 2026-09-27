<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use Database\Seeders\PortfolioSeeder;
use Database\Seeders\PostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandPaletteTest extends TestCase
{
    use RefreshDatabase;

    public function test_palette_is_rendered_with_static_commands(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="command-palette"', false)
            ->assertSee('data-command-palette-open', false)
            ->assertSee('"id":"copy-email"', false)
            ->assertSee('"id":"nav-projects"', false)
            ->assertSee('data-search-url="'.route('search').'"', false);
    }

    public function test_search_index_contains_only_published_content(): void
    {
        $this->seed([PortfolioSeeder::class, PostSeeder::class]);
        Post::create(['title' => 'Draft idea', 'slug' => 'draft-idea', 'content' => '<p>x</p>', 'is_published' => false]);
        Project::where('slug', 'shopify-app')->update(['case_study_published' => true]);

        $response = $this->getJson(route('search'))->assertOk();
        $items = collect($response->json('items'));

        $this->assertCount(6, $items->where('type', 'post'));
        $this->assertSame(['Shopify App'], $items->where('type', 'project')->pluck('title')->values()->all());
        $this->assertFalse($items->contains('title', 'Draft idea'));
        $this->assertTrue($items->contains('url', route('blog.show', 'shopify-webhooks-in-laravel')));
    }

    public function test_german_search_index_uses_german_urls_and_titles(): void
    {
        $this->seed([PortfolioSeeder::class, PostSeeder::class]);
        Project::where('slug', 'shopify-app')->update(['case_study_published' => true]);

        $items = collect($this->getJson(route('de.search'))->assertOk()->json('items'));

        $this->assertTrue($items->contains('title', 'Shopify-App'));
        $this->assertTrue($items->contains('url', route('de.projects.show', 'shopify-app')));
        $this->assertTrue($items->contains('url', route('de.blog.show', 'shopify-webhooks-in-laravel')));
    }
}
