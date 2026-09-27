<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Helpers for admin forms with English/German fields (named like `title[en]`, `title[de]`)
 * and ↑/↓ ordering.
 */
trait HandlesContentInput
{
    /** Validation rules for a translatable field: English required (optional), German optional. */
    protected function translatableRules(string $field, bool $required = true, int $max = 255): array
    {
        $rules = [$field => ['nullable', 'array']];

        foreach (Locale::supported() as $locale) {
            $isRequired = $required && $locale === Locale::default();
            $rules["{$field}.{$locale}"] = [$isRequired ? 'required' : 'nullable', 'string', "max:{$max}"];
        }

        return $rules;
    }

    /** ['en' => '...', 'de' => '...'] with empty translations removed. */
    protected function translated(Request $request, string $field): ?array
    {
        $values = collect(Locale::supported())
            ->mapWithKeys(fn ($locale) => [$locale => trim((string) $request->input("{$field}.{$locale}", ''))])
            // The rich-text editor submits "<p><br></p>" for an empty field.
            ->filter(fn ($value) => trim(html_entity_decode(strip_tags($value, '<img><iframe><video>'))) !== '')
            ->all();

        return $values ?: null;
    }

    /** One item per line, per locale: ['en' => ['a', 'b'], 'de' => [...]]. */
    protected function translatedLines(Request $request, string $field): ?array
    {
        $values = collect(Locale::supported())
            ->mapWithKeys(fn ($locale) => [$locale => $this->splitLines((string) $request->input("{$field}.{$locale}", ''))])
            ->filter(fn ($lines) => $lines !== [])
            ->all();

        return $values ?: null;
    }

    /** Comma- or newline-separated list, de-duplicated case-insensitively. */
    protected function list(Request $request, string $field): ?array
    {
        $items = collect(preg_split('/[\r\n,]+/', (string) $request->input($field, '')))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique(fn ($item) => mb_strtolower($item))
            ->values()
            ->all();

        return $items ?: null;
    }

    protected function splitLines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /** Swap the model's sort position with its neighbour. */
    protected function moveModel(Model $model, string $direction): void
    {
        $query = $model->newQuery()->orderBy('sort_order')->orderBy('id');
        $items = $query->get();

        // Normalise positions first so gaps and duplicates don't break swapping.
        DB::transaction(function () use ($items, $model, $direction) {
            $ids = $items->pluck('id')->values();
            $index = $ids->search($model->getKey());
            $target = $direction === 'up' ? $index - 1 : $index + 1;

            if ($index === false || $target < 0 || $target >= $ids->count()) {
                return;
            }

            $ordered = $ids->all();
            [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

            foreach ($ordered as $position => $id) {
                $model->newQuery()->whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        // Query-builder updates skip model events, so clear the cached content explicitly.
        \App\Support\Portfolio::flush();
    }

    protected function nextSortOrder(string $modelClass): int
    {
        return (int) $modelClass::query()->max('sort_order') + 1;
    }
}
