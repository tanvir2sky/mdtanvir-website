<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use ClearsPortfolioCache, HasTranslations;

    protected $fillable = [
        'company', 'url', 'role', 'period', 'is_current',
        'focus', 'highlights', 'tags', 'sort_order',
    ];

    protected array $translatable = ['role', 'focus', 'highlights'];

    protected function casts(): array
    {
        return [
            'role' => 'array',
            'focus' => 'array',
            'highlights' => 'array',
            'tags' => 'array',
            'is_current' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
