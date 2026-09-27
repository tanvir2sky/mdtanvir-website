<?php

namespace App\Services;

use App\Support\Http\SafeHttp;
use App\Support\Http\UnsafeRequestException;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

/**
 * Builds a quick health report for a public Shopify store from its homepage and public JSON endpoints.
 * Reports contain only data (check ids, statuses, numbers); the view turns them into text in any language.
 */
class ShopifyStoreInspector
{
    /** Check id => weight in the overall score. */
    public const WEIGHTS = [
        'response_time' => 15,
        'page_weight' => 10,
        'viewport' => 8,
        'title' => 10,
        'meta_description' => 10,
        'h1' => 8,
        'image_alt' => 12,
        'lazy_loading' => 5,
        'open_graph' => 8,
        'canonical' => 5,
        'lang' => 4,
        'catalogue' => 5,
    ];

    public function __construct(private SafeHttp $http) {}

    /** Normalises "shop.com", "https://shop.com/path" or "x.myshopify.com" to a hostname. */
    public static function normalizeHost(string $input): string
    {
        $input = trim($input);

        if ($input === '') {
            throw new UnsafeRequestException(UnsafeRequestException::INVALID_URL);
        }

        if (! preg_match('#^[a-z][a-z0-9+.-]*://#i', $input)) {
            $input = "https://{$input}";
        }

        $parts = parse_url($input);
        $scheme = strtolower($parts['scheme'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true) || isset($parts['user']) || isset($parts['port'])) {
            throw new UnsafeRequestException(UnsafeRequestException::INVALID_URL);
        }

        $host = strtolower(rtrim($parts['host'] ?? '', '.'));

        if (! SafeHttp::isValidHostname($host)) {
            throw new UnsafeRequestException(UnsafeRequestException::INVALID_URL);
        }

        return $host;
    }

    public function inspect(string $host): array
    {
        $policy = $this->redirectPolicy($host);
        $home = $this->http->get("https://{$host}/", $policy);
        $finalHost = strtolower((string) parse_url($home['url'], PHP_URL_HOST));

        $meta = $this->json("https://{$finalHost}/meta.json", $policy);
        $isShopify = isset($meta['myshopify_domain'])
            || isset($home['headers']['x-shopid'])
            || isset($home['headers']['x-shopify-stage'])
            || str_contains(strtolower(implode(' ', $home['headers']['powered-by'] ?? [])), 'shopify');

        if (! $isShopify) {
            return ['host' => $finalHost, 'is_shopify' => false, 'checked_at' => now()->toIso8601String()];
        }

        $products = $this->json("https://{$finalHost}/products.json?limit=250", $policy);
        $collections = $this->json("https://{$finalHost}/collections.json?limit=250", $policy);
        $html = $this->analyseHtml($home['body']);

        $facts = [
            'name' => $meta['name'] ?? null,
            'myshopify_domain' => $meta['myshopify_domain'] ?? null,
            'currency' => $meta['currency'] ?? null,
            'country' => $meta['country'] ?? null,
            'products' => is_array($products['products'] ?? null) ? count($products['products']) : null,
            'collections' => is_array($collections['collections'] ?? null) ? count($collections['collections']) : null,
            'response_ms' => $home['ms'],
            'html_kb' => (int) round(strlen($home['body']) / 1024),
        ] + $html;

        $checks = $this->checks($facts);

        return [
            'host' => $finalHost,
            'is_shopify' => true,
            'score' => $this->score($checks),
            'facts' => $facts,
            'checks' => $checks,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /** Allow www/non-www and myshopify.com hops; a *.myshopify.com store may redirect to its primary domain. */
    private function redirectPolicy(string $origin): callable
    {
        $bare = fn (string $host) => preg_replace('/^www\./', '', $host);

        return fn (string $from, string $to) => $bare($from) === $bare($to)
            || $bare($origin) === $bare($to)
            || str_ends_with($to, '.myshopify.com')
            || str_ends_with($origin, '.myshopify.com');
    }

    private function json(string $url, callable $policy): ?array
    {
        try {
            $response = $this->http->get($url, $policy);
        } catch (UnsafeRequestException) {
            return null;
        }

        if ($response['status'] !== 200) {
            return null;
        }

        $data = json_decode($response['body'], true);

        return is_array($data) ? $data : null;
    }

    private function analyseHtml(string $body): array
    {
        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$body, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);
        $meta = fn (string $query) => trim((string) ($xpath->query($query)->item(0)?->getAttribute('content') ?? ''));

        $images = $xpath->query('//img');
        $missingAlt = 0;
        $lazy = 0;
        foreach ($images as $image) {
            if (! $image->hasAttribute('alt') || trim($image->getAttribute('alt')) === '') {
                $missingAlt++;
            }
            if (strtolower($image->getAttribute('loading')) === 'lazy') {
                $lazy++;
            }
        }

        return [
            'title' => Str::limit(trim((string) ($xpath->query('//title')->item(0)?->textContent ?? '')), 200, ''),
            'meta_description' => Str::limit($meta('//meta[@name="description"]'), 400, ''),
            'h1_count' => $xpath->query('//h1')->length,
            'images' => $images->length,
            'images_missing_alt' => $missingAlt,
            'images_lazy' => $lazy,
            'og_title' => $meta('//meta[@property="og:title"]') !== '',
            'og_image' => $meta('//meta[@property="og:image"]') !== '',
            'canonical' => $xpath->query('//link[@rel="canonical"]')->length > 0,
            'lang' => trim((string) ($xpath->query('//html')->item(0)?->getAttribute('lang') ?? '')),
            'viewport' => $xpath->query('//meta[@name="viewport"]')->length > 0,
        ];
    }

    /** @return array<int, array{id: string, status: string, value: mixed}> */
    private function checks(array $f): array
    {
        $titleLength = mb_strlen($f['title']);
        $descriptionLength = mb_strlen($f['meta_description']);
        $altRatio = $f['images'] > 0 ? $f['images_missing_alt'] / $f['images'] : 0;
        $lazyRatio = $f['images'] > 0 ? $f['images_lazy'] / $f['images'] : 1;

        $checks = [
            'response_time' => [$f['response_ms'] < 800 ? 'pass' : ($f['response_ms'] < 1800 ? 'warn' : 'fail'), $f['response_ms']],
            'page_weight' => [$f['html_kb'] < 150 ? 'pass' : ($f['html_kb'] < 400 ? 'warn' : 'fail'), $f['html_kb']],
            'viewport' => [$f['viewport'] ? 'pass' : 'fail', null],
            'title' => [$titleLength === 0 ? 'fail' : ($titleLength >= 30 && $titleLength <= 60 ? 'pass' : 'warn'), $titleLength],
            'meta_description' => [$descriptionLength === 0 ? 'fail' : ($descriptionLength >= 70 && $descriptionLength <= 160 ? 'pass' : 'warn'), $descriptionLength],
            'h1' => [$f['h1_count'] === 1 ? 'pass' : ($f['h1_count'] === 0 ? 'fail' : 'warn'), $f['h1_count']],
            'image_alt' => [$altRatio === 0 ? 'pass' : ($altRatio <= 0.2 ? 'warn' : 'fail'), $f['images_missing_alt']],
            'lazy_loading' => [$f['images'] > 6 && $lazyRatio < 0.3 ? 'warn' : 'pass', $f['images_lazy']],
            'open_graph' => [$f['og_title'] && $f['og_image'] ? 'pass' : ($f['og_title'] || $f['og_image'] ? 'warn' : 'fail'), null],
            'canonical' => [$f['canonical'] ? 'pass' : 'warn', null],
            'lang' => [$f['lang'] !== '' ? 'pass' : 'warn', $f['lang']],
            'catalogue' => [$f['products'] === null || $f['products'] > 0 ? 'pass' : 'warn', $f['products']],
        ];

        return collect($checks)
            ->map(fn ($check, $id) => ['id' => $id, 'status' => $check[0], 'value' => $check[1]])
            ->values()
            ->all();
    }

    private function score(array $checks): int
    {
        $points = ['pass' => 1, 'warn' => 0.5, 'fail' => 0];
        $total = 0;
        $earned = 0;

        foreach ($checks as $check) {
            $weight = self::WEIGHTS[$check['id']];
            $total += $weight;
            $earned += $weight * $points[$check['status']];
        }

        return (int) round($earned / $total * 100);
    }
}
