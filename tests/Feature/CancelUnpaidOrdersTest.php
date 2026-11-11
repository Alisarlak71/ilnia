<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\SendOrderConfirmationSms;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CancelUnpaidOrdersTest extends TestCase
{
    use RefreshDatabase;

    private const VERIFY_URL = 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json';

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        config(['shop.unpaid_order_hours' => 24]);
        $this->variant = ProductVariant::factory()->create(['price' => 480000, 'stock' => 5]);
    }

    private function enableGateway(): void
    {
        config(['services.zarinpal.merchant_id' => '00000000-0000-0000-0000-000000000000', 'services.zarinpal.sandbox' => true]);
    }

    private function order(OrderStatus $status, int $hoursAgo): Order
    {
        $order = Order::factory()->for(User::factory())->create([
            'status' => $status, 'subtotal' => 960000, 'shipping_cost' => 60000, 'total' => 1020000,
        ]);
        $order->items()->create([
            'product_variant_id' => $this->variant->id, 'product_name' => 'عسل نمونه', 'variant_title' => '۱ کیلوگرم',
            'weight_grams' => 1000, 'unit_price' => 480000, 'quantity' => 2, 'line_total' => 960000,
        ]);
        $order->forceFill(['created_at' => now()->subHours($hoursAgo)])->saveQuietly();

        return $order;
    }

    private function payment(Order $order, PaymentStatus $status, int $hoursAgo, ?string $authority = 'S000000000000000000000000000000AUTH1'): void
    {
        $payment = $order->payments()->create(['gateway' => 'zarinpal', 'amount' => $order->total, 'status' => $status, 'authority' => $authority]);
        $payment->forceFill(['created_at' => now()->subHours($hoursAgo)])->saveQuietly();
    }

    private function runCleanup(): void
    {
        $this->artisan('ilnia:cancel-unpaid-orders')->assertSuccessful();
    }

    public function test_old_unpaid_orders_are_cancelled(): void
    {
        Http::fake();
        $pending = $this->order(OrderStatus::PendingPayment, 25);
        $failed = $this->order(OrderStatus::PaymentFailed, 30);
        $this->payment($failed, PaymentStatus::Failed, 30);

        $this->runCleanup();

        $this->assertSame(OrderStatus::Cancelled, $pending->fresh()->status);
        $this->assertSame(OrderStatus::Cancelled, $failed->fresh()->status);
        $this->assertSame(5, $this->variant->fresh()->stock, 'Stock is not touched.');
        Http::assertNothingSent();
    }

    public function test_recent_orders_and_recent_payment_attempts_are_kept(): void
    {
        Http::fake();
        $recent = $this->order(OrderStatus::PendingPayment, 23);
        $retried = $this->order(OrderStatus::PaymentFailed, 48);
        $this->payment($retried, PaymentStatus::Failed, 2); // "pay again" two hours ago

        $this->runCleanup();

        $this->assertSame(OrderStatus::PendingPayment, $recent->fresh()->status);
        $this->assertSame(OrderStatus::PaymentFailed, $retried->fresh()->status);
    }

    public function test_paid_and_other_orders_are_never_touched(): void
    {
        Http::fake();
        $statuses = [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::NeedsAttention, OrderStatus::Cancelled];
        $orders = array_map(fn ($s) => $this->order($s, 100), $statuses);

        $this->runCleanup();

        foreach ($orders as $i => $order) {
            $this->assertSame($statuses[$i], $order->fresh()->status);
        }
    }

    public function test_unsettled_payment_that_was_actually_paid_is_recorded_not_cancelled(): void
    {
        $this->enableGateway();
        Http::fake([self::VERIFY_URL => Http::response(['data' => ['code' => 100, 'ref_id' => 777], 'errors' => []])]);
        $order = $this->order(OrderStatus::PendingPayment, 30);
        $this->payment($order, PaymentStatus::Pending, 30);

        $this->runCleanup();

        $order->refresh();
        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertSame('777', $order->payments()->sole()->ref_id);
        $this->assertSame(3, $this->variant->fresh()->stock);
        Queue::assertPushed(SendOrderConfirmationSms::class);
    }

    public function test_unsettled_payment_that_was_not_paid_is_failed_then_cancelled(): void
    {
        $this->enableGateway();
        Http::fake([self::VERIFY_URL => Http::response(['data' => [], 'errors' => ['code' => -51, 'message' => 'Session is not valid']], 400)]);
        $order = $this->order(OrderStatus::PendingPayment, 30);
        $this->payment($order, PaymentStatus::Pending, 30);

        $this->runCleanup();

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(PaymentStatus::Failed, $order->payments()->sole()->status);
        $this->assertSame(5, $this->variant->fresh()->stock);
    }

    public function test_gateway_unreachable_keeps_order_for_next_run(): void
    {
        $this->enableGateway();
        Http::fake(fn () => throw new ConnectionException('timeout'));
        $order = $this->order(OrderStatus::PendingPayment, 30);
        $this->payment($order, PaymentStatus::Pending, 30);

        $this->runCleanup();

        $this->assertSame(OrderStatus::PendingPayment, $order->fresh()->status);
        $this->assertSame(PaymentStatus::Pending, $order->payments()->sole()->status);
    }

    public function test_unsettled_payment_while_gateway_disabled_is_kept(): void
    {
        Http::fake();
        $order = $this->order(OrderStatus::PendingPayment, 30);
        $this->payment($order, PaymentStatus::Pending, 30);

        $this->runCleanup();

        $this->assertSame(OrderStatus::PendingPayment, $order->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_pending_payment_without_authority_is_failed_and_order_cancelled(): void
    {
        Http::fake();
        $order = $this->order(OrderStatus::PendingPayment, 30);
        $this->payment($order, PaymentStatus::Pending, 30, authority: null);

        $this->runCleanup();

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(PaymentStatus::Failed, $order->payments()->sole()->status);
    }

    public function test_command_is_scheduled_hourly(): void
    {
        $event = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
            ->first(fn ($e) => str_contains((string) $e->command, 'ilnia:cancel-unpaid-orders'));

        $this->assertNotNull($event);
        $this->assertSame('0 * * * *', $event->expression);
    }
}
