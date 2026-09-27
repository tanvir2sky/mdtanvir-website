<?php

namespace App\Support;

use Illuminate\Http\Request;

/** Anonymous visitor identity: a keyed hash of IP + user agent. Raw IPs are never stored. */
class Visitor
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|embedly|preview|monitor|headless|lighthouse|curl|wget|python-requests|httpclient/i';

    public static function hash(?Request $request = null): string
    {
        $request ??= request();

        return hash_hmac('sha256', $request->ip().'|'.$request->userAgent(), (string) config('app.key'));
    }

    public static function isBot(?Request $request = null): bool
    {
        $agent = (string) ($request ?? request())->userAgent();

        return $agent === '' || (bool) preg_match(self::BOT_PATTERN, $agent);
    }
}
