<?php

namespace App\Http\Controllers;

class ToolsController extends Controller
{
    /** Tools listed on /tools, the ⌘K palette and the sitemap. */
    public static function tools(): array
    {
        return [
            [
                'route' => 'tools.shopify-check',
                'icon' => 'fab fa-shopify',
                'accent' => 'emerald',
                'title' => __('Shopify store health check'),
                'description' => __('Enter any Shopify store and get a quick report on speed, SEO basics and catalogue size, with suggestions.'),
            ],
            [
                'route' => 'tools.hmac',
                'icon' => 'fas fa-shield-halved',
                'accent' => 'violet',
                'title' => __('Shopify webhook HMAC verifier'),
                'description' => __('Check whether a webhook signature matches its payload and secret. Runs entirely in your browser.'),
            ],
            [
                'route' => 'tools.cron',
                'icon' => 'fas fa-clock',
                'accent' => 'sky',
                'title' => __('Cron expression explainer'),
                'description' => __('Turn a cron expression into plain language, see the next run times and get the matching Laravel scheduler method.'),
            ],
        ];
    }

    public function index()
    {
        return view('tools.index', ['tools' => self::tools()]);
    }

    public function hmac()
    {
        return view('tools.hmac');
    }

    public function cron()
    {
        return view('tools.cron');
    }
}
