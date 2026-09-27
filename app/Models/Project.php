<?php

namespace App\Models;

use App\Models\Concerns\ClearsPortfolioCache;
use App\Models\Concerns\HasTranslations;
use App\Support\HtmlToc;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use ClearsPortfolioCache, HasTranslations;

    /** Accent palettes (full class names so Tailwind can detect them). */
    public const ACCENTS = [
        'cyan' => [
            'tile' => 'bg-cyan-500/10 text-cyan-600 ring-cyan-500/20 dark:text-cyan-300',
            'label' => 'text-cyan-600 dark:text-cyan-300',
            'glow' => 'rgba(6, 182, 212, 0.16)',
            'cover' => 'from-cyan-400 via-sky-500 to-violet-500',
        ],
        'sky' => [
            'tile' => 'bg-sky-500/10 text-sky-600 ring-sky-500/20 dark:text-sky-300',
            'label' => 'text-sky-600 dark:text-sky-300',
            'glow' => 'rgba(14, 165, 233, 0.16)',
            'cover' => 'from-sky-400 via-primary-500 to-indigo-600',
        ],
        'emerald' => [
            'tile' => 'bg-emerald-500/10 text-emerald-600 ring-emerald-500/20 dark:text-emerald-300',
            'label' => 'text-emerald-600 dark:text-emerald-300',
            'glow' => 'rgba(16, 185, 129, 0.16)',
            'cover' => 'from-emerald-400 via-teal-500 to-cyan-600',
        ],
        'violet' => [
            'tile' => 'bg-violet-500/10 text-violet-600 ring-violet-500/20 dark:text-violet-300',
            'label' => 'text-violet-600 dark:text-violet-300',
            'glow' => 'rgba(139, 92, 246, 0.16)',
            'cover' => 'from-violet-500 via-purple-500 to-fuchsia-500',
        ],
        'rose' => [
            'tile' => 'bg-rose-500/10 text-rose-600 ring-rose-500/20 dark:text-rose-300',
            'label' => 'text-rose-600 dark:text-rose-300',
            'glow' => 'rgba(244, 63, 94, 0.16)',
            'cover' => 'from-rose-500 via-orange-500 to-amber-400',
        ],
    ];

    protected $fillable = [
        'slug', 'title', 'category', 'summary', 'icon', 'accent', 'tags',
        'is_featured', 'is_visible', 'sort_order',
        'role', 'year', 'duration', 'live_url', 'repo_url',
        'challenge', 'approach', 'outcome', 'body', 'cover_image', 'case_study_published',
    ];

    protected array $translatable = [
        'title', 'category', 'summary', 'role', 'challenge', 'approach', 'outcome', 'body',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'category' => 'array',
            'summary' => 'array',
            'tags' => 'array',
            'role' => 'array',
            'challenge' => 'array',
            'approach' => 'array',
            'outcome' => 'array',
            'body' => 'array',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'case_study_published' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_visible', true);
    }

    public function scopeWithPublishedCaseStudy(Builder $query): void
    {
        $query->where('is_visible', true)->where('case_study_published', true);
    }

    public function hasCaseStudy(): bool
    {
        return $this->is_visible && $this->case_study_published;
    }

    public function accentStyle(): array
    {
        return self::ACCENTS[$this->accent] ?? self::ACCENTS['sky'];
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }

    /** @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>} */
    public function bodyWithToc(): array
    {
        return HtmlToc::build((string) $this->t('body'));
    }
}
