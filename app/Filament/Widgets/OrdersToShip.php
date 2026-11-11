<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Support\Jalali;
use App\Support\Toman;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/** Paid orders that still have to be prepared and posted, oldest payment first. */
class OrdersToShip extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('سفارش‌های آمادهٔ ارسال')
            ->query(Order::query()->whereIn('status', [OrderStatus::Paid, OrderStatus::Processing]))
            ->defaultSort('paid_at')
            ->columns([
                TextColumn::make('number')->label('شماره'),
                TextColumn::make('recipient_name')->label('گیرنده'),
                TextColumn::make('city')->label('شهر')
                    ->formatStateUsing(fn (Order $record) => $record->province.'، '.$record->city),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('total')->label('مبلغ')
                    ->formatStateUsing(fn (int $state) => Toman::format($state)),
                TextColumn::make('paid_at')->label('تاریخ پرداخت')
                    ->formatStateUsing(fn ($state) => Jalali::dateTime($state)),
            ])
            ->recordUrl(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('سفارشی منتظر ارسال نیست')
            ->emptyStateDescription('سفارش‌های پرداخت‌شده اینجا نمایش داده می‌شوند.')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
