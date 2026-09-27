<?php

use App\Support\Locale;

if (! function_exists('lroute')) {
    /** URL of a public route in the current (or given) locale. */
    function lroute(string $name, mixed $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        return Locale::route($name, $parameters, $locale, $absolute);
    }
}

if (! function_exists('locale_switch_url')) {
    /** The current page in another locale. */
    function locale_switch_url(string $locale): string
    {
        return Locale::switchUrl($locale);
    }
}
