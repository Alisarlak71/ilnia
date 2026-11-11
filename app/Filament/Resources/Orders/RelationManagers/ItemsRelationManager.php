<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Support\Digits;
use App\Support\Toman;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Order lines (snapshot). Read-only. */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'اقلام سفارش';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->paginated(false)
            ->columns([
                TextColumn::make('product_name')->label('محصول'),
                TextColumn::make('variant_title')->label('وزن'),
                TextColumn::make('quantity')->label('تعداد')
                    ->formatStateUsing(fn (int $state) => Digits::toPersian($state)),
                TextColumn::make('unit_price')->label('قیمت واحد')
                    ->formatStateUsing(fn (int $state) => Toman::format($state)),
                TextColumn::make('line_total')->label('جمع')
                    ->formatStateUsing(fn (int $state) => Toman::format($state)),
            ]);
    }
}
