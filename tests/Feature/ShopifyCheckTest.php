<?php

namespace Tests\Feature;

use App\Models\StoreCheck;
use App\Models\User;
use App\Support\Http\HostResolver;
use App\Support\Http\SafeHttp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShopifyCheckTest extends TestCase
{
    use RefreshDatabase;

    private const STORE_HTML = <<<'HTML'
<!doctype html><html lang="en"><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Acme Outdoor Gear | Tents, Packs and Trail Shoes</title>
<meta name="description" content="Shop durable outdoor gear: tents, backpacks and trail shoes tested on real mountains. Free shipping over $50.">
<meta property="og:title" content="Acme Outdoor Gear">
<meta property="og:image" content="https://shop.example.com/og.jpg">
<link rel="canonical" href="https://shop.example.com/">
</head><body><h1>Gear for every trail</h1>
<img src="a.jpg" alt="Tent"><img src="b.jpg" alt="Backpack" loading="lazy"><img src="c.jpg">
</body></html>
HTML;

    protected function setUp(): void
    {
        parent::setUp();

        // Resolve every host to a public IP, except the ones a test makes private.
        $this->app->instance(HostResolver::class, new class implements HostResolver
        {
            public array $map = [
                'internal.example.com' => ['10.1.2.3'],
                'sneaky.example.com' => ['93.184.216.34', '127.0.0.1'],
                'www.shop3.example.com' => ['169.254.169.254'],
            ];

            public function resolve(string $host): array
            {
                return $this->map[$host] ?? ['93.184.216.34'];
            }
        });
    }

    private function fakeStore(): void
    {
        Http::fake([
            'https://shop.example.com/' => Http::response(self::STORE_HTML, 200, ['x-shopid' => '123']),
            'https://shop.example.com/meta.json' => Http::response(['name' => 'Acme Outdoor', 'myshopify_domain' => 'acme.myshopify.com', 'currency' => 'USD']),
            'https://shop.example.com/products.json*' => Http::response(['products' => array_fill(0, 42, ['id' => 1])]),
            'https://shop.example.com/collections.json*' => Http::response(['collections' => array_fill(0, 5, ['id' => 1])]),
        ]);
    }

    public function test_form_renders_without_a_store(): void
    {
        Http::fake();

        $this->get(route('tools.shopify-check'))->assertOk()->assertSee('Check store');

        Http::assertNothingSent();
    }

    public function test_shopify_store_gets_a_scored_report(): void
    {
        $this->fakeStore();

        $response = $this->get(route('tools.shopify-check', ['store' => 'https://shop.example.com/collections/all']));

        $response->assertOk()
            ->assertSee('Acme Outdoor')
            ->assertSee('Server response time')
            ->assertSee('1 of 3 images have no alt text')
            ->assertSee('Want help fixing these?')
            ->assertSee('USD')
            ->assertSee('>42<', false);

        $this->assertMatchesRegularExpression('/aria-label="Score: (\d+) out of 100"/', $response->getContent());
        $this->assertSame(1, StoreCheck::where('host', 'shop.example.com')->where('is_shopify', true)->count());

        Http::assertSent(fn ($request) => str_starts_with($request->url(), 'https://shop.example.com/'));
        Http::assertNotSent(fn ($request) => str_starts_with($request->url(), 'http://'));
    }

    public function test_http_input_is_upgraded_to_https(): void
    {
        $this->fakeStore();

        $this->get(route('tools.shopify-check', ['store' => 'http://shop.example.com']))->assertOk()->assertSee('Acme Outdoor');
    }

    public function test_non_shopify_site_gets_a_friendly_message(): void
    {
        Http::fake([
            'https://blog.example.com/' => Http::response('<html><title>Blog</title></html>'),
            'https://blog.example.com/*' => Http::response('Not found', 404),
        ]);

        $this->get(route('tools.shopify-check', ['store' => 'blog.example.com']))
            ->assertOk()
            ->assertSee('does not look like a Shopify store');
    }

    public function test_unsafe_targets_are_rejected_without_any_request(): void
    {
        Http::fake();

        foreach (['localhost', '127.0.0.1', '10.0.0.5', '[::1]', 'https://user:pass@shop.example.com', 'shop.example.com:8080', 'ftp://shop.example.com', 'printer.local', 'nodots'] as $input) {
            $this->get(route('tools.shopify-check', ['store' => $input]))
                ->assertOk()
                ->assertSee('Enter a valid store address', false);
        }

        $this->get(route('tools.shopify-check', ['store' => 'internal.example.com']))->assertSee('That address cannot be checked.');
        $this->get(route('tools.shopify-check', ['store' => 'sneaky.example.com']))->assertSee('That address cannot be checked.');

        Http::assertNothingSent();
        $this->assertSame(0, StoreCheck::count());
    }

    public function test_redirects_to_other_hosts_or_private_addresses_are_blocked(): void
    {
        Http::fake([
            'https://shop.example.com/' => Http::response('', 302, ['Location' => 'https://evil.example.org/']),
        ]);

        $this->get(route('tools.shopify-check', ['store' => 'shop.example.com']))
            ->assertSee('redirects to a different website');

        // An allowed www redirect is still re-checked: this host resolves to a cloud metadata address.
        Http::fake([
            'https://shop3.example.com/' => Http::response('', 301, ['Location' => 'https://www.shop3.example.com/']),
        ]);

        $this->get(route('tools.shopify-check', ['store' => 'shop3.example.com']))
            ->assertSee('That address cannot be checked.');

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'evil.example.org') || str_contains($request->url(), 'www.shop3.example.com'));
    }

    public function test_redirect_loops_are_capped(): void
    {
        Http::fake([
            'https://loop.example.com/*' => Http::response('', 302, ['Location' => '/again']),
            'https://loop.example.com/' => Http::response('', 302, ['Location' => '/again']),
        ]);

        $this->get(route('tools.shopify-check', ['store' => 'loop.example.com']))
            ->assertSee('redirects too many times');

        Http::assertSentCount(SafeHttp::MAX_REDIRECTS + 1);
    }

    public function test_www_redirect_is_followed(): void
    {
        Http::fake([
            'https://acme.example.com/' => Http::response('', 301, ['Location' => 'https://www.acme.example.com/']),
            'https://www.acme.example.com/' => Http::response(self::STORE_HTML, 200, ['powered-by' => 'Shopify']),
            'https://www.acme.example.com/*' => Http::response(['products' => []]),
        ]);

        $this->get(route('tools.shopify-check', ['store' => 'acme.example.com']))
            ->assertOk()
            ->assertSee('www.acme.example.com')
            ->assertSee('No products are publicly listed yet.');
    }

    public function test_reports_are_cached_per_host(): void
    {
        $this->fakeStore();

        $this->get(route('tools.shopify-check', ['store' => 'shop.example.com']))->assertOk();
        $sent = count(Http::recorded());

        $this->get(route('tools.shopify-check', ['store' => 'SHOP.example.com']))->assertOk()->assertSee('Acme Outdoor');

        $this->assertCount($sent, Http::recorded());
        $this->assertSame(2, StoreCheck::count());
    }

    public function test_checks_are_rate_limited(): void
    {
        $this->fakeStore();

        for ($i = 0; $i < 10; $i++) {
            $this->get(route('tools.shopify-check', ['store' => 'shop.example.com']))->assertOk();
        }

        $this->get(route('tools.shopify-check', ['store' => 'shop.example.com']))
            ->assertStatus(429)
            ->assertSee('You have run a lot of checks');
    }

    public function test_honeypot_skips_the_check(): void
    {
        Http::fake();

        $this->get(route('tools.shopify-check', ['store' => 'shop.example.com', 'website' => 'x']))->assertOk();

        Http::assertNothingSent();
    }

    public function test_german_report(): void
    {
        $this->fakeStore();

        $this->get(route('de.tools.shopify-check', ['store' => 'shop.example.com']))
            ->assertOk()
            ->assertSee('Server-Antwortzeit')
            ->assertSee('Brauchst du Hilfe bei der Umsetzung?');
    }

    public function test_admin_sees_store_checks(): void
    {
        $this->fakeStore();
        $this->get(route('tools.shopify-check', ['store' => 'shop.example.com']));

        $this->actingAs(User::factory()->create(['email' => config('app.admin_email')]))
            ->get(route('admin.store-checks.index'))
            ->assertOk()
            ->assertSee('shop.example.com');
    }

    public function test_public_ip_detection(): void
    {
        foreach (['127.0.0.1', '10.0.0.1', '172.16.5.4', '192.168.1.1', '169.254.169.254', '0.0.0.0', '100.64.1.1', '::1', 'fc00::1', 'fe80::1', '::ffff:10.0.0.1'] as $ip) {
            $this->assertFalse(SafeHttp::isPublicIp($ip), "{$ip} should not be public");
        }

        foreach (['8.8.8.8', '93.184.216.34', '2606:4700:4700::1111'] as $ip) {
            $this->assertTrue(SafeHttp::isPublicIp($ip), "{$ip} should be public");
        }
    }
}
