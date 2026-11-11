<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Forms\IntegerInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('نام دسته')
                            ->placeholder('مثلاً عسل')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('نامک (آدرس)')
                            ->helperText('خالی بگذارید تا خودکار از نام ساخته شود.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        IntegerInput::make('sort')
                            ->label('ترتیب نمایش')
                            ->helperText('عدد کمتر، زودتر نمایش داده می‌شود.')
                            ->default(0)
                            ->required(),
                    ]),
            ]);
    }
}
