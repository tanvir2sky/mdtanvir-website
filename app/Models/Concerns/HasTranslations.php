<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

/**
 * Stores translatable attributes as JSON keyed by locale: {"en": "...", "de": "..."}.
 *
 * Models list their translatable attributes in `$translatable` and cast them to array.
 */
trait HasTranslations
{
    /** Value of a translatable attribute in the given (or current) locale, falling back to English. */
    public function t(string $attribute, ?string $locale = null): mixed
    {
        $values = $this->getAttribute($attribute);

        if (! is_array($values)) {
            return $values;
        }

        $locale ??= App::getLocale();
        $fallback = config('app.fallback_locale', 'en');

        foreach ([$locale, $fallback] as $candidate) {
            $value = $values[$candidate] ?? null;

            if ($value !== null && $value !== '' && $value !== []) {
                return $value;
            }
        }

        return null;
    }

    /** Whether a translation exists (non-empty) for the locale. */
    public function hasTranslation(string $attribute, string $locale): bool
    {
        $value = $this->getAttribute($attribute)[$locale] ?? null;

        return $value !== null && $value !== '' && $value !== [];
    }

    public function translatableAttributes(): array
    {
        return $this->translatable ?? [];
    }
}
