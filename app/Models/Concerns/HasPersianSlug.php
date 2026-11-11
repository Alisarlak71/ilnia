<?php

namespace App\Models\Concerns;

use App\Support\Digits;
use Illuminate\Support\Str;

/**
 * Fill the `slug` column from `name` when it is left empty, keeping Persian letters:
 *   «عسل عشایری» → "عسل-عشایری"   (not the unreadable transliteration "aasl-aashayry")
 * Duplicate slugs get a number: "عسل-عشایری-2".
 */
trait HasPersianSlug
{
    protected static function bootHasPersianSlug(): void
    {
        static::saving(function (self $model) {
            $source = filled($model->slug) ? $model->slug : $model->name;

            $model->slug = $model->uniqueSlug(static::makeSlug((string) $source));
        });
    }

    public static function makeSlug(string $value): string
    {
        $value = Digits::toEnglish($value);
        $value = str_replace("\u{200C}", '', $value); // zero-width non-joiner (نیم‌فاصله)

        return Str::slug($value, '-', null);
    }

    protected function uniqueSlug(string $slug): string
    {
        $slug = $slug !== '' ? $slug : Str::lower(Str::random(8));
        $candidate = $slug;
        $i = 2;

        while (static::query()
            ->where('slug', $candidate)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->exists()) {
            $candidate = $slug.'-'.$i++;
        }

        return $candidate;
    }
}
