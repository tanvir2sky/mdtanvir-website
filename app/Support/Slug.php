<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slug
{
    /**
     * A unique slug for the model's table, adding -2, -3… when the slug is taken.
     *
     * @param  class-string<Model>  $model
     */
    public static function unique(string $model, string $source, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug($source) ?: 'item';
        $slug = $base;

        for ($i = 2; $model::query()
            ->where($column, $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
