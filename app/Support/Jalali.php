<?php

namespace App\Support;

use DateTimeInterface;
use IntlDateFormatter;

/**
 * Display dates in the Persian (Jalali/Shamsi) calendar with Persian digits.
 * The database keeps normal timestamps; convert only when displaying.
 * Uses PHP's intl extension (also required by Filament) — no extra package.
 *
 *   Jalali::date($order->created_at)      // "۱۴۰۵/۰۸/۲۰"
 *   Jalali::long($order->created_at)      // "۲۰ آبان ۱۴۰۵"
 *   Jalali::dateTime($order->created_at)  // "۱۴۰۵/۰۸/۲۰ ۲۲:۳۰"
 */
final class Jalali
{
    /**
     * @param  string  $pattern  ICU pattern, e.g. "yyyy/MM/dd", "d MMMM yyyy", "EEEE d MMMM yyyy".
     */
    public static function format(?DateTimeInterface $date, string $pattern = 'yyyy/MM/dd'): string
    {
        if ($date === null) {
            return '';
        }

        $formatter = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            config('app.timezone'),
            IntlDateFormatter::TRADITIONAL,
            $pattern,
        );

        // ICU output may contain invisible direction marks; remove them.
        return str_replace(["\u{200E}", "\u{200F}"], '', (string) $formatter->format($date));
    }

    public static function date(?DateTimeInterface $date): string
    {
        return self::format($date, 'yyyy/MM/dd');
    }

    public static function long(?DateTimeInterface $date): string
    {
        return self::format($date, 'd MMMM yyyy');
    }

    public static function dateTime(?DateTimeInterface $date): string
    {
        return self::format($date, 'yyyy/MM/dd HH:mm');
    }
}
