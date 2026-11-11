<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Support\Digits;
use App\Support\Toman;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['category', 'variants', 'images']))
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('main_image')
                    ->label('عکس')
                    ->disk('public')
                    ->state(fn (Product $record) => $record->mainImage()?->path)
                    ->square(),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('دسته'),
                TextColumn::make('variants_summary')
                    ->label('وزن‌ها')
                    ->state(fn (Product $record) => $record->variants->pluck('title')->join('، ') ?: '—'),
                TextColumn::make('starting_price')
                    ->label('قیمت از')
                    ->state(fn (Product $record) => $record->startingPrice())
                    ->formatStateUsing(fn (?int $state) => $state === null ? 'بدون قیمت' : Toman::format($state))
                    ->placeholder('بدون قیمت'),
                TextColumn::make('total_stock')
                    ->label('موجودی')
                    ->state(fn (Product $record) => Digits::toPersian((int) $record->variants->sum('stock'))),
                ToggleColumn::make('is_active')
                    ->label('در فروشگاه'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('دسته')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active')
                    ->label('نمایش در فروشگاه'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
