<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_pages_render_in_both_languages(): void
    {
        $this->get(route('tools.index'))->assertOk()
            ->assertSee('Free developer tools')
            ->assertSee(route('tools.hmac'), false)
            ->assertSee(route('tools.cron'), false)
            ->assertSee(route('tools.shopify-check'), false);

        $this->get(route('tools.hmac'))->assertOk()->assertSee('id="hmac-tool"', false)->assertSee('hash_equals');
        $this->get(route('tools.cron'))->assertOk()->assertSee('id="cron-tool"', false)->assertSee('*/15 9-17 * * 1-5');

        $this->get(route('de.tools.index'))->assertOk()->assertSee('Kostenlose Entwickler-Tools');
        $this->get(route('de.tools.cron'))->assertOk()->assertSee('Cron-Ausdruck-Erklärer')->assertSee('"everyNMinutes":"Alle :n Minuten"', false);
    }

    public function test_tools_are_linked_from_nav_palette_and_sitemap(): void
    {
        $this->get(route('blog.index'))
            ->assertSee('href="'.route('tools.index').'"', false)
            ->assertSee('"id":"nav-tool-cron"', false);

        $this->get(route('sitemap'))
            ->assertSee(route('tools.hmac'), false)
            ->assertSee(route('de.tools.shopify-check'), false);
    }
}
