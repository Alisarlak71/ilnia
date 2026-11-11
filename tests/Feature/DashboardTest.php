<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Filament\Widgets\LowStock;
use App\Filament\Widgets\OrdersToShip;
use App\Filament\Widgets\StoreStats;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    private function order(OrderStatus $status, int $total, ?string $paidAt = null): Order
    {
        $order = Order::factory()->create(['status' => $status, 'total' => $total]);
        $order->forceFill(['paid_at' => $paidAt === null ? null : now()->modify($paidAt)])->saveQuietly();

        return $order;
    }

    public function test_dashboard_loads_with_widgets(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSeeLivewire(StoreStats::class)
            ->assertSeeLivewire(OrdersToShip::class)
            ->assertSeeLivewire(LowStock::class);
    }

    public function test_customer_cannot_open_dashboard(): void
    {
        $this->actingAs(User::create(['mobile' => '09121234567']))->get('/admin')->assertForbidden();
    }

    public function test_sales_count_only_received_money(): void
    {
        $this->order(OrderStatus::Paid, 1000000, 'now');
        $this->order(OrderStatus::Delivered, 500000, '-3 days');
        $this->order(OrderStatus::Shipped, 200000, '-40 days');        // older than 30 days
        $this->order(OrderStatus::PendingPayment, 9000000);            // not paid
        $this->order(OrderStatus::Cancelled, 8000000);
        $this->order(OrderStatus::NeedsAttention, 7000000, 'now');     // money may be returned
        $this->order(OrderStatus::Processing, 300000, 'now');

        Livewire::test(StoreStats::class)
            ->assertSee('۱٬۳۰۰٬۰۰۰')   // today: 1,000,000 + 300,000
            ->assertSee('۱٬۸۰۰٬۰۰۰')   // 30 days: + 500,000
            ->assertSee('۲ سفارش پرداخت‌شده')
            ->assertSee('۳ سفارش پرداخت‌شده');
    }

    public function test_to_ship_and_attention_counts(): void
    {
        $this->order(OrderStatus::Paid, 100, 'now');
        $this->order(OrderStatus::Processing, 100, 'now');
        $this->order(OrderStatus::Shipped, 100, 'now');
        $this->order(OrderStatus::NeedsAttention, 100, 'now');

        Livewire::test(StoreStats::class)
            ->assertSeeInOrder(['آمادهٔ ارسال', '۲'])
            ->assertSeeInOrder(['نیاز به بررسی', '۱']);
    }

    public function test_orders_to_ship_lists_paid_and_processing_only(): void
    {
        $paid = $this->order(OrderStatus::Paid, 100, '-1 day');
        $processing = $this->order(OrderStatus::Processing, 100, 'now');
        $shipped = $this->order(OrderStatus::Shipped, 100, 'now');
        $pending = $this->order(OrderStatus::PendingPayment, 100);

        Livewire::test(OrdersToShip::class)
            ->assertCanSeeTableRecords([$paid, $processing], inOrder: true)
            ->assertCanNotSeeTableRecords([$shipped, $pending]);
    }

    public function test_low_stock_lists_active_low_variants(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $out = ProductVariant::factory()->for($product)->create(['stock' => 0, 'is_active' => true]);
        $low = ProductVariant::factory()->for($product)->create(['stock' => 5, 'is_active' => true]);
        $enough = ProductVariant::factory()->for($product)->create(['stock' => 6, 'is_active' => true]);
        $inactive = ProductVariant::factory()->for($product)->create(['stock' => 0, 'is_active' => false]);
        $hiddenProduct = ProductVariant::factory()->for(Product::factory()->create(['is_active' => false]))->create(['stock' => 0]);

        Livewire::test(LowStock::class)
            ->assertCanSeeTableRecords([$out, $low], inOrder: true)
            ->assertCanNotSeeTableRecords([$enough, $inactive, $hiddenProduct])
            ->assertSee('تمام شده');
    }
}
