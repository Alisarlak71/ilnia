<?php

namespace App\Filament\Resources\ShippingMethods\Tables;

use App\Models\ShippingMethod;
use App\Support\Toman;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('cost')
                    ->label('هزینه')
                    ->formatStateUsing(fn (int $state) => Toman::format($state)),
                TextColumn::make('free_over_weight_grams')
                    ->label('رایگان از')
                    ->state(fn (ShippingMethod $record) => $record->free_over_weight_grams === null
                        ? 'ندارد'
                        : Toman::number($record->free_over_weight_grams).' گرم'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
