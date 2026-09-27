<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Project;
use App\Models\SkillGroup;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['email' => config('app.admin_email')]);
    }

    public function test_seeder_is_idempotent_and_keeps_admin_edits(): void
    {
        $this->seed(PortfolioSeeder::class);

        $this->assertSame(3, Experience::count());
        $this->assertSame(5, SkillGroup::count());
        $this->assertSame(4, Project::count());
        $this->assertSame(0, Project::where('case_study_published', true)->count());

        Experience::where('company', 'Altruan GmbH')->first()->update(['role' => ['en' => 'Senior Engineer']]);

        $this->seed(PortfolioSeeder::class);

        $this->assertSame(3, Experience::count());
        $this->assertSame(5, SkillGroup::count());
        $this->assertSame('Senior Engineer', Experience::where('company', 'Altruan GmbH')->first()->t('role'));
    }

    public function test_admin_pages_require_admin(): void
    {
        $this->get(route('admin.projects.index'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['email' => 'someone@example.com']))
            ->get(route('admin.projects.index'))
            ->assertForbidden();
    }

    public function test_admin_lists_render(): void
    {
        $this->seed(PortfolioSeeder::class);
        $admin = $this->admin();

        foreach (['admin.experiences.index', 'admin.projects.index', 'admin.skills.index', 'admin.experiences.create', 'admin.projects.create', 'admin.skills.create'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }

        $this->actingAs($admin)->get(route('admin.projects.edit', Project::first()))->assertOk()->assertSee('Case study page');
        $this->actingAs($admin)->get(route('admin.experiences.edit', Experience::first()))->assertOk()->assertSee('fas fa-brain | AI features');
        $this->actingAs($admin)->get(route('admin.skills.edit', SkillGroup::first()))->assertOk();
    }

    public function test_admin_creates_experience_with_translations_and_home_shows_it(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.experiences.store'), [
                'company' => 'Acme Corp',
                'url' => 'https://acme.test',
                'period' => '2015 - 2018',
                'role' => ['en' => 'Intern Developer', 'de' => 'Praktikant Entwicklung'],
                'highlights' => ['en' => "Built things\nFixed bugs", 'de' => "Dinge gebaut\n\nFehler behoben"],
                'focus' => ['en' => "fas fa-star | Speed | Fast delivery\nQuality | Tested code"],
                'tags' => 'PHP, php, MySQL',
            ])
            ->assertRedirect(route('admin.experiences.index'));

        $experience = Experience::where('company', 'Acme Corp')->firstOrFail();
        $this->assertSame(['en' => ['Built things', 'Fixed bugs'], 'de' => ['Dinge gebaut', 'Fehler behoben']], $experience->highlights);
        $this->assertSame(['PHP', 'MySQL'], $experience->tags);
        $this->assertSame(['icon' => 'fas fa-star', 'title' => 'Speed', 'text' => 'Fast delivery'], $experience->focus['en'][0]);
        $this->assertSame('Quality', $experience->focus['en'][1]['title']);

        $this->get(route('home'))->assertSee('Intern Developer')->assertSee('3 yrs');
        $this->get(route('de.home'))->assertSee('Praktikant Entwicklung')->assertSee('3 Jahre');
    }

    public function test_english_is_required_but_german_is_optional(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.skills.create'))
            ->post(route('admin.skills.store'), [
                'icon' => 'fas fa-code',
                'name' => ['en' => '', 'de' => 'Nur Deutsch'],
                'items' => ['en' => 'One'],
            ])
            ->assertSessionHasErrors('name.en');

        $this->actingAs($admin)
            ->post(route('admin.skills.store'), [
                'icon' => 'fas fa-code',
                'name' => ['en' => 'Testing'],
                'items' => ['en' => "PHPUnit\nPest"],
            ])
            ->assertRedirect(route('admin.skills.index'));

        $group = SkillGroup::firstOrFail();
        $this->assertSame(['en' => 'Testing'], $group->name);

        // German falls back to English when no translation exists.
        app()->setLocale('de');
        $this->assertSame('Testing', $group->t('name'));
    }

    public function test_admin_updates_project_and_publishes_case_study(): void
    {
        $this->seed(PortfolioSeeder::class);
        $project = Project::where('slug', 'shopify-app')->firstOrFail();

        $this->get(route('home'))->assertDontSee(route('projects.show', 'shopify-app'), false);

        $this->actingAs($this->admin())
            ->put(route('admin.projects.update', $project), [
                'title' => ['en' => 'Shopify Inventory App', 'de' => 'Shopify-Lager-App'],
                'summary' => ['en' => 'Keeps stock in sync.'],
                'icon' => 'fab fa-shopify',
                'accent' => 'emerald',
                'tags' => 'Laravel, Shopify API',
                'is_visible' => '1',
                'case_study_published' => '1',
                'challenge' => ['en' => 'Stock drifted between systems.'],
                'body' => ['en' => '<h2>Architecture</h2><p>Webhooks.</p>', 'de' => '<p><br></p>'],
            ])
            ->assertRedirect(route('admin.projects.index'));

        $project->refresh();
        $this->assertSame('shopify-inventory-app', $project->slug);
        $this->assertTrue($project->case_study_published);
        $this->assertSame(['en' => '<h2>Architecture</h2><p>Webhooks.</p>'], $project->body, 'Empty editor markup is not stored as a translation');

        $this->get(route('home'))
            ->assertSee('Shopify Inventory App')
            ->assertSee(route('projects.show', 'shopify-inventory-app'), false);
    }

    public function test_reordering_moves_items_and_updates_home_order(): void
    {
        $this->seed(PortfolioSeeder::class);
        $admin = $this->admin();
        $backend = SkillGroup::where('name->en', 'Backend')->firstOrFail();
        $frontend = SkillGroup::where('name->en', 'Frontend')->firstOrFail();

        $this->actingAs($admin)->patch(route('admin.skills.move', [$frontend, 'up']))->assertRedirect();

        $this->assertLessThan($backend->fresh()->sort_order, $frontend->fresh()->sort_order);

        $html = $this->get(route('home'))->getContent();
        $this->assertLessThan(strpos($html, '>Backend<'), strpos($html, '>Frontend<'));
    }

    public function test_terminal_data_cache_is_cleared_after_edits(): void
    {
        $this->seed(PortfolioSeeder::class);
        $this->get(route('home'))->assertSee('E-commerce Platform');

        Project::where('slug', 'e-commerce-platform')->first()->update(['title' => ['en' => 'Headless Commerce']]);

        $terminal = $this->get(route('home'))->getContent();
        $this->assertStringContainsString('Headless Commerce', $terminal);
        $this->assertStringNotContainsString('"name":"E-commerce Platform"', $terminal);
    }

    public function test_admin_can_delete_content(): void
    {
        $this->seed(PortfolioSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.projects.destroy', Project::first()))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.experiences.destroy', Experience::first()))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.skills.destroy', SkillGroup::first()))->assertRedirect();

        $this->assertSame(3, Project::count());
        $this->assertSame(2, Experience::count());
        $this->assertSame(4, SkillGroup::count());
    }
}
