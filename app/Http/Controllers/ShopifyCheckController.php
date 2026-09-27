<?php

namespace App\Http\Controllers;

use App\Models\StoreCheck;
use App\Services\ShopifyStoreInspector;
use App\Support\Http\UnsafeRequestException;
use App\Support\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ShopifyCheckController extends Controller
{
    public const PER_MINUTE = 10;

    public const PER_DAY = 40;

    public function __invoke(Request $request, ShopifyStoreInspector $inspector)
    {
        $input = trim((string) $request->query('store', ''));
        $view = ['store' => $input, 'report' => null, 'error' => null];

        // Honeypot: bots filling the hidden field just get the empty form back.
        if ($input === '' || filled($request->query('website'))) {
            return view('tools.shopify-check', $view);
        }

        if (mb_strlen($input) > 255) {
            return view('tools.shopify-check', ['error' => UnsafeRequestException::INVALID_URL] + $view);
        }

        try {
            $host = ShopifyStoreInspector::normalizeHost($input);
        } catch (UnsafeRequestException $e) {
            return view('tools.shopify-check', ['error' => $e->reason] + $view);
        }

        $visitor = Visitor::hash($request);

        if (RateLimiter::tooManyAttempts("store-check:minute:{$visitor}", self::PER_MINUTE)
            || RateLimiter::tooManyAttempts("store-check:day:{$visitor}", self::PER_DAY)) {
            return response()->view('tools.shopify-check', ['error' => 'rate_limited'] + $view, 429);
        }

        RateLimiter::hit("store-check:minute:{$visitor}", 60);
        RateLimiter::hit("store-check:day:{$visitor}", 86400);

        try {
            // Cached per host for an hour, so repeated checks don't hit the store again.
            $report = Cache::remember("store-check:{$host}", now()->addHour(), fn () => $inspector->inspect($host));
        } catch (UnsafeRequestException $e) {
            return view('tools.shopify-check', ['error' => $e->reason] + $view);
        }

        StoreCheck::create([
            'host' => $report['host'],
            'is_shopify' => $report['is_shopify'],
            'score' => $report['score'] ?? null,
            'visitor_hash' => $visitor,
            'locale' => app()->getLocale(),
        ]);

        return view('tools.shopify-check', ['report' => $report] + $view);
    }
}
