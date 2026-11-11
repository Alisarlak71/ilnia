<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Support\Digits;
use App\Support\Jalali;
use App\Support\Toman;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('number')->label('شماره')->searchable(),
                TextColumn::make('user.mobile')->label('موبایل مشتری')
                    ->formatStateUsing(fn (?string $state) => Digits::toPersian($state))
                    ->searchable(),
                TextColumn::make('recipient_name')->label('گیرنده'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('total')->label('مبلغ کل')
                    ->formatStateUsing(fn (int $state) => Toman::format($state)),
                TextColumn::make('created_at')->label('تاریخ ثبت')
                    ->formatStateUsing(fn ($state) => Jalali::dateTime($state))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(OrderStatus::class),
            ])
            ->recordActions([
                EditAction::make()->label('مشاهده'),
            ]);
    }
}
