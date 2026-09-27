<?php

namespace App\Models;

use App\Support\HtmlToc;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'tags',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'featured_image',
        'is_published',
        'is_featured',
        'notify_subscribers',
        'published_at',
    ];

    /** Cached result of contentWithToc(). */
    private ?array $renderedContent = null;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'notify_subscribers' => 'boolean',
            'published_at' => 'datetime',
            'newsletter_sent_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): void
    {
        $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $like = '%'.$term.'%';

        $query->where(function (Builder $query) use ($like) {
            $query->where('title', 'like', $like)
                ->orWhere('excerpt', 'like', $like)
                ->orWhere('content', 'like', $like);
        });
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->content)) / 200));
    }

    /** Colour and icon for this post's category, from config/blog.php. */
    public function categoryStyle(): array
    {
        return config("blog.categories.{$this->category}") ?? config('blog.default');
    }

    public function summary(int $limit = 160): string
    {
        return $this->excerpt ?: Str::limit(trim(strip_tags((string) $this->content)), $limit);
    }

    public function imageUrl(): ?string
    {
        return $this->featured_image ? Storage::url($this->featured_image) : null;
    }

    /**
     * Content with ids added to every h2/h3, plus a table of contents built from them.
     *
     * @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>}
     */
    public function contentWithToc(): array
    {
        return $this->renderedContent ??= HtmlToc::build((string) $this->content);
    }
}
