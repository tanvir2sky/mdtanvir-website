<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCaseStudyTest extends TestCase
{
    use RefreshDatabase;

    private function publish(string $slug, array $attributes = []): Project
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $project->update($attributes + [
            'case_study_published' => true,
            'role' => ['en' => 'Lead developer', 'de' => 'Leitender Entwickler'],
            'year' => '2024',
            'challenge' => ['en' => 'Orders were processed by hand.'],
            'body' => ['en' => '<h2>Architecture</h2><p>Queues.</p><h2>Results</h2><p>Faster.</p>'],
        ]);

        return $project;
    }

    public function test_draft_case_studies_are_not_public(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this->get(route('projects.show', 'shopify-app'))->assertNotFound();
        $this->get(route('projects.show', 'missing'))->assertNotFound();
    }

    public function test_published_case_study_renders_with_toc_and_facts(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->publish('shopify-app');

        $this->get(route('projects.show', 'shopify-app'))
            ->assertOk()
            ->assertSee('Shopify App')
            ->assertSee('Lead developer')
            ->assertSee('2024')
            ->assertSee('Orders were processed by hand.')
            ->assertSee('The challenge')
            ->assertSee('On this page')
            ->assertSee('id="architecture"', false)
            ->assertSee('href="#architecture"', false)
            ->assertSee('"@type":"CreativeWork"', false)
            ->assertSee('Work with me');
    }

    public function test_german_case_study_uses_translations_with_english_fallback(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->publish('shopify-app');

        $this->get(route('de.projects.show', 'shopify-app'))
            ->assertOk()
            ->assertSee('Shopify-App')
            ->assertSee('Leitender Entwickler')
            ->assertSee('Die Herausforderung')
            ->assertSee('Orders were processed by hand.'); // no German challenge → English fallback
    }

    public function test_hidden_projects_are_not_public_even_when_published(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->publish('shopify-app', ['is_visible' => false]);

        $this->get(route('projects.show', 'shopify-app'))->assertNotFound();
        $this->get(route('home'))->assertDontSee('Custom Shopify application built');
    }

    public function test_navigation_between_case_studies(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->publish('e-commerce-platform');
        $this->publish('shopify-app');

        $this->get(route('projects.show', 'e-commerce-platform'))
            ->assertSee(route('projects.show', 'shopify-app'), false);
        $this->get(route('projects.show', 'shopify-app'))
            ->assertSee(route('projects.show', 'e-commerce-platform'), false);
    }

    public function test_sitemap_lists_published_case_studies_in_both_languages(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->publish('shopify-app');

        $response = $this->get(route('sitemap'));

        $response->assertOk()
            ->assertSee(route('projects.show', 'shopify-app'), false)
            ->assertSee(route('de.projects.show', 'shopify-app'), false)
            ->assertDontSee(route('projects.show', 'financial-app'), false);

        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }
}
