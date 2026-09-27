<?php

namespace App\Support\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Fetches user-supplied URLs without letting them reach internal systems (SSRF protection):
 * HTTPS on port 443 only, hostnames only (no IP literals), every resolved address must be public,
 * the connection is pinned to the checked address, redirects are re-checked hop by hop,
 * and responses are size- and time-limited.
 */
class SafeHttp
{
    public const MAX_REDIRECTS = 3;

    public const MAX_BYTES = 2_000_000;

    public function __construct(private HostResolver $resolver) {}

    /**
     * @param  callable(string $fromHost, string $toHost): bool|null  $allowRedirect
     * @return array{status: int, headers: array, body: string, url: string, ms: int}
     */
    public function get(string $url, ?callable $allowRedirect = null): array
    {
        $started = microtime(true);
        $redirects = 0;

        while (true) {
            [$host, $ip] = $this->assertSafe($url);

            try {
                $response = Http::withoutRedirecting()
                    ->connectTimeout(3)
                    ->timeout(6)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; StoreHealthCheck/1.0; +'.url('/tools/shopify-store-check').')',
                        'Accept' => 'text/html,application/json;q=0.9,*/*;q=0.8',
                    ])
                    ->withOptions([
                        // Connect to the address we just checked, so DNS can't change underneath us.
                        'curl' => [CURLOPT_RESOLVE => [$this->resolveEntry($host, $ip)]],
                        'progress' => function ($downloadTotal, $downloaded) {
                            if ($downloaded > self::MAX_BYTES || $downloadTotal > self::MAX_BYTES) {
                                throw new UnsafeRequestException(UnsafeRequestException::TOO_LARGE);
                            }
                        },
                    ])
                    ->get($url);
            } catch (ConnectionException $e) {
                if ($e->getPrevious() instanceof UnsafeRequestException) {
                    throw $e->getPrevious();
                }
                throw new UnsafeRequestException(UnsafeRequestException::UNREACHABLE, $e->getMessage());
            }

            if ($response->redirect()) {
                if (++$redirects > self::MAX_REDIRECTS) {
                    throw new UnsafeRequestException(UnsafeRequestException::TOO_MANY_REDIRECTS);
                }

                $next = $this->absoluteUrl($url, (string) $response->header('Location'));
                $nextHost = strtolower((string) parse_url($next, PHP_URL_HOST));

                if ($allowRedirect && ! $allowRedirect($host, $nextHost)) {
                    throw new UnsafeRequestException(UnsafeRequestException::REDIRECT_BLOCKED, "Redirect to {$nextHost} not allowed");
                }

                $url = $next;

                continue;
            }

            $body = $response->body();
            if (strlen($body) > self::MAX_BYTES) {
                throw new UnsafeRequestException(UnsafeRequestException::TOO_LARGE);
            }

            return [
                'status' => $response->status(),
                'headers' => array_change_key_case($response->headers(), CASE_LOWER),
                'body' => $body,
                'url' => $url,
                'ms' => (int) round((microtime(true) - $started) * 1000),
            ];
        }
    }

    /**
     * Validates the URL and returns [host, a checked public IP].
     *
     * @return array{0: string, 1: string}
     */
    public function assertSafe(string $url): array
    {
        $parts = parse_url($url);

        if (($parts['scheme'] ?? null) !== 'https' || isset($parts['user']) || isset($parts['pass'])) {
            throw new UnsafeRequestException(UnsafeRequestException::INVALID_URL, 'Only plain https URLs are allowed');
        }

        if (isset($parts['port']) && (int) $parts['port'] !== 443) {
            throw new UnsafeRequestException(UnsafeRequestException::INVALID_URL, 'Only port 443 is allowed');
        }

        $host = strtolower(rtrim($parts['host'] ?? '', '.'));

        if (! self::isValidHostname($host)) {
            throw new UnsafeRequestException(UnsafeRequestException::INVALID_URL, 'Invalid hostname');
        }

        $ips = $this->resolver->resolve($host);

        if ($ips === []) {
            throw new UnsafeRequestException(UnsafeRequestException::UNREACHABLE, 'Hostname does not resolve');
        }

        foreach ($ips as $ip) {
            if (! self::isPublicIp($ip)) {
                throw new UnsafeRequestException(UnsafeRequestException::PRIVATE_ADDRESS, "{$host} resolves to a non-public address");
            }
        }

        return [$host, $ips[0]];
    }

    /** A DNS hostname with at least one dot. IP literals and names like "localhost" are rejected. */
    public static function isValidHostname(string $host): bool
    {
        if ($host === '' || strlen($host) > 253 || filter_var(trim($host, '[]'), FILTER_VALIDATE_IP)) {
            return false;
        }

        if (! str_contains($host, '.') || Str::endsWith($host, ['.localhost', '.local', '.internal', '.lan', '.home.arpa'])) {
            return false;
        }

        return (bool) preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z][a-z0-9-]{0,61}[a-z0-9]$/', $host);
    }

    public static function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false
            // IPv4-mapped IPv6 (::ffff:10.0.0.1) and the shared/CGNAT range aren't covered by the flags above.
            && ! preg_match('/^::ffff:/i', $ip)
            && ! (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && self::inRange($ip, '100.64.0.0', 10));
    }

    private static function inRange(string $ip, string $network, int $bits): bool
    {
        $mask = -1 << (32 - $bits);

        return (ip2long($ip) & $mask) === (ip2long($network) & $mask);
    }

    private function resolveEntry(string $host, string $ip): string
    {
        return str_contains($ip, ':') ? "{$host}:443:[{$ip}]" : "{$host}:443:{$ip}";
    }

    private function absoluteUrl(string $base, string $location): string
    {
        if ($location === '') {
            throw new UnsafeRequestException(UnsafeRequestException::REDIRECT_BLOCKED, 'Empty redirect');
        }

        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $parts = parse_url($base);
        $origin = "{$parts['scheme']}://{$parts['host']}";

        if (str_starts_with($location, '//')) {
            return "{$parts['scheme']}:{$location}";
        }

        return str_starts_with($location, '/') ? $origin.$location : $origin.'/'.$location;
    }
}
