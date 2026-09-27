<?php

namespace App\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Locale
{
    /** Locale code => URL prefix. English is the default and has no prefix. */
    public const PREFIXES = ['en' => '', 'de' => 'de'];

    public const NAMES = ['en' => 'English', 'de' => 'Deutsch'];

    public static function default(): string
    {
        return 'en';
    }

    public static function supported(): array
    {
        return array_keys(self::PREFIXES);
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::PREFIXES);
    }

    public static function current(): string
    {
        $locale = App::getLocale();

        return self::isSupported($locale) ? $locale : self::default();
    }

    /** Route name for a public route in the given locale ("blog.index" → "de.blog.index"). */
    public static function routeName(string $name, ?string $locale = null): string
    {
        $name = self::baseName($name);
        $locale ??= self::current();

        return $locale === self::default() ? $name : "{$locale}.{$name}";
    }

    /** Strips any locale prefix from a route name. */
    public static function baseName(string $name): string
    {
        foreach (self::supported() as $locale) {
            if ($locale !== self::default() && str_starts_with($name, "{$locale}.")) {
                return substr($name, strlen($locale) + 1);
            }
        }

        return $name;
    }

    /** URL of a public route in the given (or current) locale. */
    public static function route(string $name, mixed $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        $localized = self::routeName($name, $locale);

        return Route::has($localized)
            ? route($localized, $parameters, $absolute)
            : route(self::baseName($name), $parameters, $absolute);
    }

    /** Same page in another locale, falling back to that locale's home page. */
    public static function switchUrl(string $locale): string
    {
        $route = request()->route();
        $name = $route?->getName();

        if ($name && Route::has(self::routeName($name, $locale))) {
            $url = self::route($name, $route->parameters(), $locale);
            $query = request()->getQueryString();

            return $query ? "{$url}?{$query}" : $url;
        }

        return self::route('home', [], $locale);
    }

    /** Alternate URLs of the current page for hreflang tags, or [] when the page isn't localised. */
    public static function alternates(): array
    {
        $route = request()->route();
        $name = $route?->getName();

        if (! $name || ! Route::has(self::routeName($name, self::default()))) {
            return [];
        }

        $alternates = [];
        foreach (self::supported() as $locale) {
            if (Route::has(self::routeName($name, $locale))) {
                $alternates[$locale] = self::route($name, $route->parameters(), $locale);
            }
        }

        return count($alternates) > 1 ? $alternates : [];
    }

    /** Open Graph locale code. */
    public static function ogLocale(?string $locale = null): string
    {
        return ['en' => 'en_US', 'de' => 'de_DE'][$locale ?? self::current()] ?? 'en_US';
    }
}
