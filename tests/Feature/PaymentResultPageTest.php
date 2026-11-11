<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentResultPageTest extends TestCase
{
    use RefreshDatabase;

    private const REQUEST_URL = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';

    private User $user;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['mobile' => '09121234567']);
        $product = Product::factory()->create(['name' => 'عسل نمونه', 'is_active' => true]);
        $this->variant = ProductVariant::factory()->for($product)->create(['price' => 480000, 'stock' => 5, 'is_active' => true]);
    }

    private function order(OrderStatus $status): Order
    {
        $order = Order::factory()->for($this->user)->create([
            'status' => $status, 'subtotal' => 960000, 'shipping_cost' => 60000, 'total' => 1020000,
            'shipping_method_name' => 'پست', 'province' => 'فارس', 'city' => 'شیراز', 'address_line' => 'خیابان نمونه',
        ]);
        $order->items()->create([
            'product_variant_id' => $this->variant->id, 'product_name' => 'عسل نمونه', 'variant_title' => '۱ کیلوگرم',
            'weight_grams' => 1000, 'unit_price' => 480000, 'quantity' => 2, 'line_total' => 960000,
        ]);

        return $order;
    }

    private function enableGateway(): void
    {
        config(['services.zarinpal.merchant_id' => '00000000-0000-0000-0000-000000000000', 'services.zarinpal.sandbox' => true]);
    }

    public function test_paid_order_shows_success_details_and_ref(): void
    {
        $order = $this->order(OrderStatus::Paid);
        $order->payments()->create(['gateway' => 'zarinpal', 'amount' => 1020000, 'status' => PaymentStatus::Paid, 'authority' => 'S1', 'ref_id' => '201']);

        $this->actingAs($this->user)->get(route('payment.result', $order))
            ->assertOk()
            ->assertSee('پرداخت موفق بود')
            ->assertSee($order->number)
            ->assertSee('۱٬۰۲۰٬۰۰۰ تومان')
            ->assertSeeInOrder(['کد پیگیری بانک', '۲۰۱'])
            ->assertSee('عسل نمونه (۱ کیلوگرم) × ۲')
            ->assertSee('فارس، شیراز، خیابان نمونه')
            ->assertDontSee('پرداخت دوباره');
    }

    public function test_needs_attention_explains_refund(): void
    {
        $this->actingAs($this->user)->get(route('payment.result', $this->order(OrderStatus::NeedsAttention)))
            ->assertSee('سفارش نیاز به بررسی دارد')
            ->assertSee('مبلغ را کامل برمی‌گردانیم')
            ->assertDontSee('پرداخت دوباره');
    }

    public function test_failed_order_offers_retry_when_gateway_enabled(): void
    {
        $this->enableGateway();

        $this->actingAs($this->user)->get(route('payment.result', $this->order(OrderStatus::PaymentFailed)))
            ->assertSee('پرداخت انجام نشد')
            ->assertSee('۷۲ ساعت')
            ->assertSee('پرداخت دوباره');
    }

    public function test_no_retry_button_while_gateway_disabled(): void
    {
        $this->actingAs($this->user)->get(route('payment.result', $this->order(OrderStatus::PaymentFailed)))
            ->assertSee('پرداخت انجام نشد')
            ->assertDontSee('پرداخت دوباره');
    }

    public function test_cancelled_order_has_no_retry(): void
    {
        $this->enableGateway();

        $this->actingAs($this->user)->get(route('payment.result', $this->order(OrderStatus::Cancelled)))
            ->assertSee('وضعیت سفارش')
            ->assertDontSee('پرداخت دوباره');
    }

    public function test_retry_sends_customer_to_gateway_with_saved_amount(): void
    {
        $this->enableGateway();
        Http::fake([self::REQUEST_URL => Http::response(['data' => ['code' => 100, 'authority' => 'S000000000000000000000000000000AUTH2'], 'errors' => []])]);
        $order = $this->order(OrderStatus::PaymentFailed);

        $this->actingAs($this->user)->post(route('payment.retry', $order))
            ->assertRedirect('https://sandbox.zarinpal.com/pg/StartPay/S000000000000000000000000000000AUTH2');

        Http::assertSent(fn (Request $r) => $r['amount'] === 10200000);
        $this->assertSame(OrderStatus::PendingPayment, $order->fresh()->status);
        $this->assertSame(1, $order->payments()->count());
    }

    public function test_retry_is_refused_when_stock_ran_out(): void
    {
        $this->enableGateway();
        Http::fake();
        $order = $this->order(OrderStatus::PaymentFailed);
        $this->variant->update(['stock' => 1]);

        $this->actingAs($this->user)->post(route('payment.retry', $order))
            ->assertRedirect(route('payment.result', $order));

        Http::assertNothingSent();
        $this->actingAs($this->user)->get(route('payment.result', $order))->assertSee('موجودی بعضی از محصولات این سفارش دیگر کافی نیست');
    }

    public function test_retry_does_nothing_for_paid_order_or_others_order(): void
    {
        $this->enableGateway();
        Http::fake();
        $paid = $this->order(OrderStatus::Paid);

        $this->actingAs($this->user)->post(route('payment.retry', $paid))->assertRedirect(route('payment.result', $paid));
        $this->actingAs(User::create(['mobile' => '09351234567']))
            ->post(route('payment.retry', $this->order(OrderStatus::PaymentFailed)))->assertNotFound();

        Http::assertNothingSent();
    }

    public function test_retry_requires_login(): void
    {
        $this->post(route('payment.retry', $this->order(OrderStatus::PaymentFailed)))->assertRedirect(route('login'));
    }
}
