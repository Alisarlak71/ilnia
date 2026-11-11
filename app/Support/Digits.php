<?php

namespace App\Support;

/**
 * Convert digits between Persian (۰-۹), Arabic (٠-٩) and English (0-9).
 *
 *   Digits::toEnglish('۰۹۱۲')  // "0912"   — always do this on user input
 *   Digits::toPersian(1405)    // "۱۴۰۵"   — for display
 */
final class Digits
{
    private const ENGLISH = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    private const PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const ARABIC = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    public static function toEnglish(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return str_replace([...self::PERSIAN, ...self::ARABIC], [...self::ENGLISH, ...self::ENGLISH], $value);
    }

    public static function toPersian(string|int|null $value): string
    {
        if ($value === null) {
            return '';
        }

        return str_replace([...self::ENGLISH, ...self::ARABIC], [...self::PERSIAN, ...self::PERSIAN], (string) $value);
    }
}
