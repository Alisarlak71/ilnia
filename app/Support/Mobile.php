<?php

namespace App\Support;

/**
 * Iranian mobile numbers are stored as "09xxxxxxxxx" (11 English digits).
 *
 *   Mobile::normalize('+98 912 123 4567')  // "09121234567"
 *   Mobile::normalize('۰۹۱۲۱۲۳۴۵۶۷')       // "09121234567"
 *   Mobile::normalize('12345')             // null (invalid)
 */
final class Mobile
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = Digits::toEnglish($value);

        // Keep digits only: drops "+", spaces, dashes, brackets, etc.
        $digits = preg_replace('/\D/u', '', $value);

        // 0098 9xx…  /  98 9xx…  /  09xx…  /  9xx…
        if (str_starts_with($digits, '0098')) {
            $digits = substr($digits, 4);
        } elseif (str_starts_with($digits, '98') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (! preg_match('/^9\d{9}$/', $digits)) {
            return null;
        }

        return '0'.$digits;
    }

    public static function isValid(?string $value): bool
    {
        return self::normalize($value) !== null;
    }
}
