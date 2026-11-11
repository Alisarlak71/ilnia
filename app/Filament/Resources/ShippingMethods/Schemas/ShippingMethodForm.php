<?php

namespace App\Filament\Resources\ShippingMethods\Schemas;

use App\Filament\Forms\IntegerInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShippingMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('نام')
                            ->placeholder('مثلاً پست')
                            ->required()
                            ->maxLength(100),
                        IntegerInput::make('cost')
                            ->label('هزینهٔ ارسال')
                            ->suffix('تومان')
                            ->required(),
                        IntegerInput::make('free_over_weight_grams')
                            ->label('ارسال رایگان از وزن')
                            ->suffix('گرم')
                            ->helperText('سفارش‌هایی که وزن کلشان این مقدار یا بیشتر است، رایگان ارسال می‌شوند (۴۰۰۰ گرم = ۴ کیلوگرم). خالی = ارسال رایگان نداریم.'),
                        IntegerInput::make('sort')
                            ->label('ترتیب نمایش')
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
