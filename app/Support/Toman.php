<?php

namespace App\Support;

/**
 * Money is stored as an integer number of Toman (never float).
 *
 *   Toman::format(450000)      // "۴۵۰٬۰۰۰ تومان"
 *   Toman::number(450000)      // "۴۵۰٬۰۰۰"
 *   Toman::parse('۴۵۰,۰۰۰')    // 450000   (user input → integer)
 */
final class Toman
{
    /** Persian thousands separator (U+066C). */
    public const SEPARATOR = '٬';

    public static function number(int $amount): string
    {
        $formatted = number_format(abs($amount), 0, '', self::SEPARATOR);

        return ($amount < 0 ? '−' : '').Digits::toPersian($formatted);
    }

    public static function format(int $amount): string
    {
        return self::number($amount).' '.__('money.toman');
    }

    /**
     * Turn user input (Persian/English digits, separators, "تومان") into an integer.
     * Returns null when the input has no digits or contains a decimal part.
     */
    public static function parse(string|int|null $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        $value = Digits::toEnglish((string) $value);

        // A decimal part ("1500.5" or Persian decimal "٫") is not a valid Toman amount.
        if (preg_match('/\d[.٫]\d/u', $value)) {
            return null;
        }

        $digits = preg_replace('/\D/u', '', $value);

        if ($digits === '' || strlen($digits) > 15) {
            return null;
        }

        return (int) $digits;
    }
}
