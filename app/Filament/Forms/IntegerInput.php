<?php

namespace App\Filament\Forms;

use App\Support\Toman;
use Closure;
use Filament\Forms\Components\TextInput;

/**
 * A text field for whole numbers (prices in Toman, grams, stock) that accepts
 * Persian or English digits and separators: «۴۵۰٬۰۰۰», "450,000" and "450000"
 * are all saved as the integer 450000.
 *
 *   IntegerInput::make('price')->suffix('تومان')
 */
final class IntegerInput
{
    public static function make(string $name): TextInput
    {
        return TextInput::make($name)
            ->inputMode('numeric')
            ->formatStateUsing(static fn ($state) => is_numeric($state) ? number_format((int) $state) : $state)
            ->rule(static fn () => static function (string $attribute, mixed $value, Closure $fail): void {
                if (filled($value) && Toman::parse($value) === null) {
                    $fail('یک عدد صحیح وارد کنید (بدون اعشار).');
                }
            })
            ->dehydrateStateUsing(static fn ($state) => filled($state) ? Toman::parse($state) : null);
    }
}
