<?php

namespace App\Models\Concerns;

use App\Support\Portfolio;

/** Clears the cached portfolio data whenever a content model changes. */
trait ClearsPortfolioCache
{
    public static function bootClearsPortfolioCache(): void
    {
        static::saved(fn () => Portfolio::flush());
        static::deleted(fn () => Portfolio::flush());
    }
}
