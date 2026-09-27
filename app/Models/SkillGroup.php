<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SkillGroup extends Model
{
    use ClearsPortfolioCache, HasTranslations;

    protected $fillable = ['name', 'icon', 'items', 'sort_order'];

    protected array $translatable = ['name', 'items'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'items' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
