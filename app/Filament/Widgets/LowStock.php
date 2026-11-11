<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ProductVariant;
use App\Support\Digits;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/** Active weights of active products whose stock is low (or finished), lowest first. */
class LowStock extends TableWidget
{
    public const THRESHOLD = 5;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('موجودی کم')
            ->description('وزن‌هایی که موجودی‌شان '.Digits::toPersian(self::THRESHOLD).' عدد یا کمتر است.')
            ->query(
                ProductVariant::query()
                    ->with('product')
                    ->where('is_active', true)
                    ->whereHas('product', fn ($q) => $q->where('is_active', true))
                    ->where('stock', '<=', self::THRESHOLD)
            )
            ->defaultSort('stock')
            ->columns([
                TextColumn::make('product.name')->label('محصول'),
                TextColumn::make('title')->label('وزن'),
                TextColumn::make('stock')->label('موجودی')
                    ->formatStateUsing(fn (int $state) => $state === 0 ? 'تمام شده' : Digits::toPersian($state))
                    ->badge()
                    ->color(fn (int $state) => $state === 0 ? 'danger' : 'warning'),
            ])
            ->recordUrl(fn (ProductVariant $record) => ProductResource::getUrl('edit', ['record' => $record->product_id]))
            ->emptyStateHeading('موجودی همهٔ محصولات کافی است')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
