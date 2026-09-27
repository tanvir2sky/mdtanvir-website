<?php

namespace Tests\Feature;

use Database\Seeders\PortfolioSeeder;
use Database\Seeders\PostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PortfolioSeeder::class, PostSeeder::class]);
    }

    public function test_english_home_is_the_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Technical strengths')
            ->assertSee('<link rel="alternate" hreflang="de" href="'.url('/de').'"', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/').'"', false)
            ->assertSee('content="en_US"', false);
    }

    public function test_german_home_is_translated(): void
    {
        $this->get('/de')
            ->assertOk()
            ->assertSee('<html lang="de"', false)
            ->assertSee('Technische Stärken')
            ->assertSee('Beruflicher Werdegang')
            ->assertSee('Softwareentwickler')
            ->assertSee('KI & LLM')
            ->assertSee('Entwicklung und Wartung skalierbarer Webanwendungen')
            ->assertSee('content="de_DE"', false)
            ->assertSee('"locale":"de"', false)
            ->assertDontSee('Technical strengths');
    }

    public function test_language_switcher_points_to_the_same_page(): void
    {
        $this->get('/blog?category=Laravel')
            ->assertSee('href="'.e(url('/de/blog?category=Laravel')).'"', false);

        $this->get('/de/blog')
            ->assertSee('href="'.url('/blog').'"', false);
    }

    public function test_links_stay_in_the_current_language(): void
    {
        $this->get('/de')
            ->assertSee('href="'.url('/de/blog').'"', false)
            ->assertSee(url('/de/blog/shipping-llm-features-in-laravel'), false);
    }

    public function test_german_blog_marks_articles_as_english(): void
    {
        $this->get('/de/blog')
            ->assertOk()
            ->assertSee('Notizen aus dem')
            ->assertSee('Artikel auf Englisch');

        $this->get('/de/blog/laravel-queue-jobs-that-survive-production')
            ->assertOk()
            ->assertSee('<html lang="de"', false)
            ->assertSee('Dieser Artikel ist auf Englisch verfasst.')
            ->assertSee('<article class="article-content"  lang="en"', false)
            ->assertSee('<link rel="canonical" href="'.url('/blog/laravel-queue-jobs-that-survive-production').'"', false)
            ->assertDontSee('<link rel="alternate" hreflang="de"', false)
            ->assertSee('Auf dieser Seite');
    }

    public function test_german_dates_are_translated(): void
    {
        $this->get('/de/blog/laravel-queue-jobs-that-survive-production')
            ->assertSee(now()->subDays(11)->locale('de')->translatedFormat('F d, Y'));
    }

    public function test_contact_form_redirects_back_to_german_page_with_german_errors(): void
    {
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret_key' => null]);

        $response = $this->post(route('contact.store'), [
            'locale' => 'de',
            'name' => 'Max',
            'email' => 'kein-email',
            'subject' => 'Hallo',
            'message' => 'Test',
        ]);

        $response->assertRedirect(url('/de').'#contact');
        $this->assertStringContainsString('gültige E-Mail-Adresse', session('errors')->first('email'));
    }

    public function test_contact_success_message_is_german(): void
    {
        Notification::fake();
        config(['services.turnstile.site_key' => null, 'services.turnstile.secret_key' => null]);

        $this->post(route('contact.store'), [
            'locale' => 'de',
            'name' => 'Max',
            'email' => 'max@example.com',
            'subject' => 'Hallo',
            'message' => 'Test',
        ])->assertRedirect(url('/de').'#contact');

        $this->assertSame('Danke, deine Nachricht wurde erfolgreich gesendet. Ich melde mich bald bei dir.', session('contact_status'));
    }

    public function test_unknown_locale_prefix_is_not_found(): void
    {
        $this->get('/fr')->assertNotFound();
    }
}
