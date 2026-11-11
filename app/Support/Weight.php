<?php

namespace App\Support;

/**
 * Weights are stored in grams (integer). Display helpers with Persian digits.
 *
 *   Weight::kg(3000)  // "۳"
 *   Weight::kg(1500)  // "۱٫۵"
 *   Weight::kg(250)   // "۰٫۲۵"
 */
final class Weight
{
    /** Persian decimal separator (U+066B). */
    public const DECIMAL = '٫';

    public static function kg(int $grams): string
    {
        $kg = rtrim(rtrim(number_format($grams / 1000, 2, '.', ''), '0'), '.');

        return Digits::toPersian(str_replace('.', self::DECIMAL, $kg));
    }
}
