<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Support\Jalali;
use App\Support\Toman;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Payment attempts for the order. Read-only: payments are recorded only by the payment flow. */
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'پرداخت‌ها';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('authority')
            ->paginated(false)
            ->columns([
                TextColumn::make('gateway')->label('درگاه'),
                TextColumn::make('amount')->label('مبلغ')
                    ->formatStateUsing(fn (int $state) => Toman::format($state)),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('ref_id')->label('کد پیگیری بانک')->placeholder('—'),
                TextColumn::make('created_at')->label('زمان')
                    ->formatStateUsing(fn ($state) => Jalali::dateTime($state)),
                TextColumn::make('verified_at')->label('تأیید')
                    ->formatStateUsing(fn ($state) => Jalali::dateTime($state))
                    ->placeholder('—'),
            ]);
    }
}
