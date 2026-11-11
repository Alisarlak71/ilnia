<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Support\Digits;
use App\Support\Toman;
use Carbon\CarbonInterface;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

/**
 * Dashboard numbers, all from real orders (no estimates):
 * sales today, sales in the last 30 days (with a daily chart), orders waiting to be shipped,
 * and orders that need the admin's attention.
 * "Sales" = orders whose money has been received and kept (paid → delivered), by paid_at.
 */
class StoreStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $from = now()->subDays(29)->startOfDay();

        $todayQuery = $this->sold()->where('paid_at', '>=', $today);
        $monthQuery = $this->sold()->where('paid_at', '>=', $from);

        $toShip = Order::whereIn('status', [OrderStatus::Paid, OrderStatus::Processing])->count();
        $attention = Order::where('status', OrderStatus::NeedsAttention)->count();

        return [
            Stat::make('فروش امروز (تومان)', Toman::number((int) (clone $todayQuery)->sum('total')))
                ->description(Digits::toPersian($todayQuery->count()).' سفارش پرداخت‌شده'),

            Stat::make('فروش ۳۰ روز گذشته (تومان)', Toman::number((int) (clone $monthQuery)->sum('total')))
                ->description(Digits::toPersian($monthQuery->count()).' سفارش پرداخت‌شده')
                ->chart($this->dailyTotals($from))
                ->color('primary'),

            Stat::make('آمادهٔ ارسال', Digits::toPersian($toShip))
                ->description($toShip > 0 ? 'پرداخت‌شده یا در حال آماده‌سازی' : 'سفارشی منتظر ارسال نیست')
                ->color($toShip > 0 ? 'info' : 'gray')
                ->url(OrderResource::getUrl('index')),

            Stat::make('نیاز به بررسی', Digits::toPersian($attention))
                ->description($attention > 0 ? 'مثلاً پرداخت شده ولی موجودی کافی نبوده' : 'موردی نیست')
                ->descriptionColor($attention > 0 ? 'danger' : 'gray')
                ->color($attention > 0 ? 'danger' : 'gray')
                ->url(OrderResource::getUrl('index')),
        ];
    }

    /** Orders whose money has been received and kept. */
    private function sold(): Builder
    {
        return Order::query()->whereIn('status', [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered]);
    }

    /** Sales per day for the last 30 days (oldest first), for the small chart. @return array<int, int> */
    private function dailyTotals(CarbonInterface $from): array
    {
        $byDay = $this->sold()
            ->where('paid_at', '>=', $from)
            ->get(['paid_at', 'total'])
            ->groupBy(fn (Order $order) => $order->paid_at->toDateString())
            ->map(fn ($orders) => (int) $orders->sum('total'));

        return collect(range(29, 0))
            ->map(fn (int $daysAgo) => $byDay->get(now()->subDays($daysAgo)->toDateString(), 0))
            ->all();
    }
}
