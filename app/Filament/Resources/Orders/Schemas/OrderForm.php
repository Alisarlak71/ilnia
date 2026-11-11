<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\Digits;
use App\Support\Jalali;
use App\Support\Toman;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        $money = fn (?int $state) => $state === null ? '—' : Toman::format($state);

        return $schema
            ->columns(1)
            ->components([
                Section::make('وضعیت و ارسال')
                    ->description('وضعیت‌های پرداخت را فقط درگاه تعیین می‌کند. اینجا فقط مراحل بعد از پرداخت تغییر می‌کنند.')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('وضعیت')
                            ->options(fn (?Order $record) => self::statusOptions($record))
                            ->disabled(fn (?Order $record) => $record === null || $record->status->adminTransitions() === [])
                            ->selectablePlaceholder(false)
                            ->required(),
                        TextInput::make('tracking_code')
                            ->label('کد رهگیری پست')
                            ->helperText('برای وضعیت «ارسال‌شده» لازم است. مشتری آن را در صفحهٔ «سفارش‌های من» می‌بیند.')
                            ->maxLength(100)
                            ->requiredIf('status', OrderStatus::Shipped->value)
                            ->validationMessages(['required_if' => 'برای وضعیت «ارسال‌شده»، کد رهگیری پست را وارد کنید.'])
                            ->extraInputAttributes(['dir' => 'ltr']),
                    ]),

                Section::make('اطلاعات سفارش')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('number')->label('شمارهٔ سفارش'),
                        TextEntry::make('user.mobile')->label('موبایل مشتری')
                            ->formatStateUsing(fn (?string $state) => Digits::toPersian($state))->placeholder('—'),
                        TextEntry::make('created_at')->label('تاریخ ثبت')
                            ->formatStateUsing(fn ($state) => Jalali::dateTime($state)),
                        TextEntry::make('paid_at')->label('تاریخ پرداخت')
                            ->formatStateUsing(fn ($state) => Jalali::dateTime($state))->placeholder('پرداخت نشده'),
                        TextEntry::make('shipping_method_name')->label('روش ارسال'),
                        TextEntry::make('total_weight_grams')->label('وزن کل')
                            ->formatStateUsing(fn (int $state) => Toman::number($state).' گرم'),
                        TextEntry::make('subtotal')->label('جمع اقلام')->formatStateUsing($money),
                        TextEntry::make('shipping_cost')->label('هزینهٔ ارسال')
                            ->formatStateUsing(fn (int $state) => $state === 0 ? 'رایگان' : Toman::format($state)),
                        TextEntry::make('discount')->label('تخفیف')->formatStateUsing($money),
                        TextEntry::make('total')->label('مبلغ کل')->formatStateUsing($money)->weight('bold'),
                        TextEntry::make('customer_note')->label('یادداشت مشتری')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make('گیرنده')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('recipient_name')->label('نام گیرنده'),
                        TextEntry::make('recipient_mobile')->label('موبایل گیرنده')
                            ->formatStateUsing(fn (string $state) => Digits::toPersian($state)),
                        TextEntry::make('postal_code')->label('کد پستی')
                            ->formatStateUsing(fn (string $state) => Digits::toPersian($state)),
                        TextEntry::make('address')->label('نشانی')
                            ->state(fn (Order $record) => $record->addressSummary())
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /** Current status plus the statuses the admin may move to. @return array<string, string> */
    public static function statusOptions(?Order $record): array
    {
        if ($record === null) {
            return [];
        }

        $options = [$record->status->value => $record->status->getLabel()];

        foreach ($record->status->adminTransitions() as $status) {
            $options[$status->value] = $status->getLabel();
        }

        return $options;
    }
}
