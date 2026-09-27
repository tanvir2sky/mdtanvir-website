<?php

namespace App\Models;

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
            'published_at' => 'datetime',
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
        if ($this->renderedContent !== null) {
            return $this->renderedContent;
        }

        $toc = [];
        $used = [];

        $html = preg_replace_callback(
            '/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
            function (array $match) use (&$toc, &$used) {
                [$full, $level, $attributes, $inner] = $match + [2 => '', 3 => ''];
                $text = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5));

                if ($text === '') {
                    return $full;
                }

                if (preg_match('/\sid=["\']([^"\']+)["\']/i', $attributes, $existing)) {
                    $id = $existing[1];
                } else {
                    $base = Str::slug($text) ?: 'section';
                    $id = $base;
                    for ($i = 2; isset($used[$id]); $i++) {
                        $id = "{$base}-{$i}";
                    }
                    $attributes .= ' id="'.e($id).'"';
                }

                $used[$id] = true;
                $toc[] = ['id' => $id, 'text' => $text, 'level' => (int) $level];

                return "<h{$level}{$attributes}>{$inner}</h{$level}>";
            },
            (string) $this->content
        );

        return $this->renderedContent = ['html' => $html ?? (string) $this->content, 'toc' => $toc];
    }
}
