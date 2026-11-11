<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Jobs\SendOrderConfirmationSms;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private const MERCHANT = '11111111-2222-3333-4444-555555555555';

    private const REQUEST_URL = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';

    private const VERIFY_URL = 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json';

    private User $user;

    private ProductVariant $variant;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        config(['services.zarinpal.merchant_id' => self::MERCHANT, 'services.zarinpal.sandbox' => true]);

        $this->user = User::create(['mobile' => '09121234567']);
        $this->variant = ProductVariant::factory()->create(['price' => 480000, 'stock' => 5]);
        $this->order = Order::factory()->for($this->user)->create(['subtotal' => 960000, 'shipping_cost' => 60000, 'total' => 1020000]);
        $this->order->items()->create([
            'product_variant_id' => $this->variant->id, 'product_name' => 'عسل نمونه', 'variant_title' => '۱ کیلوگرم',
            'weight_grams' => 1000, 'unit_price' => 480000, 'quantity' => 2, 'line_total' => 960000,
        ]);
    }

    private function service(): PaymentService
    {
        return app(PaymentService::class);
    }

    private function fakeGateway(array $verify = ['data' => ['code' => 100, 'message' => 'Verified', 'ref_id' => 201, 'card_pan' => '502229******5995'], 'errors' => []], int $verifyStatus = 200): void
    {
        Http::fake([
            self::REQUEST_URL => Http::response(['data' => ['code' => 100, 'message' => 'Success', 'authority' => 'S000000000000000000000000000000AUTH1', 'fee_type' => 'Merchant', 'fee' => 100], 'errors' => []]),
            self::VERIFY_URL => Http::response($verify, $verifyStatus),
        ]);
    }

    private function startPayment(): string
    {
        $this->service()->request($this->order);

        return Payment::sole()->authority;
    }

    // --- request ------------------------------------------------------------------

    public function test_disabled_without_merchant_id(): void
    {
        Http::fake();
        config(['services.zarinpal.merchant_id' => null]);

        $this->assertFalse($this->service()->isEnabled());

        try {
            $this->service()->request($this->order);
            $this->fail('Expected PaymentException');
        } catch (PaymentException $e) {
            $this->assertSame('پرداخت آنلاین به‌زودی فعال می‌شود.', $e->getMessage());
        }

        Http::assertNothingSent();
        $this->assertSame(0, Payment::count());
    }

    public function test_request_sends_rial_amount_from_saved_order_and_returns_startpay_url(): void
    {
        $this->fakeGateway();

        $url = $this->service()->request($this->order);

        $this->assertSame('https://sandbox.zarinpal.com/pg/StartPay/S000000000000000000000000000000AUTH1', $url);

        Http::assertSent(fn (Request $r) => $r->url() === self::REQUEST_URL
            && $r['merchant_id'] === self::MERCHANT
            && $r['amount'] === 10200000            // 1,020,000 Toman = 10,200,000 Rial
            && $r['currency'] === 'IRR'
            && $r['callback_url'] === route('payment.callback')
            && $r['metadata']['order_id'] === $this->order->number
            && $r['metadata']['mobile'] === '09121234567');

        $payment = Payment::sole();
        $this->assertSame(1020000, $payment->amount);
        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame('S000000000000000000000000000000AUTH1', $payment->authority);
        $this->assertSame(OrderStatus::PendingPayment, $this->order->fresh()->status);
    }

    public function test_production_url_when_sandbox_is_off(): void
    {
        config(['services.zarinpal.sandbox' => false]);
        Http::fake(['https://payment.zarinpal.com/pg/v4/payment/request.json' => Http::response(['data' => ['code' => 100, 'authority' => 'A0000000000000000000000000000000AUTH'], 'errors' => []])]);

        $this->assertSame('https://payment.zarinpal.com/pg/StartPay/A0000000000000000000000000000000AUTH', $this->service()->request($this->order));
    }

    public function test_request_gateway_error_marks_attempt_failed(): void
    {
        Http::fake([self::REQUEST_URL => Http::response(['data' => [], 'errors' => ['code' => -10, 'message' => 'Terminal is not valid']], 400)]);

        $this->expectException(PaymentException::class);

        try {
            $this->service()->request($this->order);
        } finally {
            $this->assertSame(PaymentStatus::Failed, Payment::sole()->status);
            $this->assertNull(Payment::sole()->authority);
        }
    }

    public function test_request_connection_error(): void
    {
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $this->expectException(PaymentException::class);

        try {
            $this->service()->request($this->order);
        } finally {
            $this->assertSame(PaymentStatus::Failed, Payment::sole()->status);
        }
    }

    public function test_paid_order_cannot_be_paid_again(): void
    {
        Http::fake();
        $this->order->update(['status' => OrderStatus::Paid]);

        $this->expectException(PaymentException::class);
        $this->service()->request($this->order);
    }

    public function test_failed_order_can_be_retried(): void
    {
        $this->fakeGateway();
        $this->order->update(['status' => OrderStatus::PaymentFailed]);

        $this->service()->request($this->order);

        $this->assertSame(OrderStatus::PendingPayment, $this->order->fresh()->status);
    }

    // --- callback & verify ----------------------------------------------------------

    public function test_successful_payment_verifies_reduces_stock_and_queues_sms(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();

        $order = $this->service()->handleCallback($authority, 'OK');

        Http::assertSent(fn (Request $r) => $r->url() === self::VERIFY_URL
            && $r['merchant_id'] === self::MERCHANT
            && $r['amount'] === 10200000
            && $r['authority'] === $authority);

        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $payment = Payment::sole();
        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertSame('201', $payment->ref_id);
        $this->assertNotNull($payment->verified_at);
        $this->assertSame(3, $this->variant->fresh()->stock); // 5 - 2
        Queue::assertPushed(SendOrderConfirmationSms::class, fn ($job) => $job->orderId === $order->id);
    }

    public function test_cancelled_at_gateway_does_not_verify(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();

        $order = $this->service()->handleCallback($authority, 'NOK');

        Http::assertNotSent(fn (Request $r) => $r->url() === self::VERIFY_URL);
        $this->assertSame(OrderStatus::PaymentFailed, $order->status);
        $this->assertSame(PaymentStatus::Cancelled, Payment::sole()->status);
        $this->assertSame(5, $this->variant->fresh()->stock);
        Queue::assertNothingPushed();
    }

    public function test_unsuccessful_payment_minus_51(): void
    {
        $this->fakeGateway(['data' => [], 'errors' => ['code' => -51, 'message' => 'Session is not valid, session is not active paid try.']], 400);
        $authority = $this->startPayment();

        $order = $this->service()->handleCallback($authority, 'OK');

        $this->assertSame(OrderStatus::PaymentFailed, $order->status);
        $this->assertSame(PaymentStatus::Failed, Payment::sole()->status);
        $this->assertSame(5, $this->variant->fresh()->stock);
        Queue::assertNothingPushed();
    }

    public function test_amount_mismatch_minus_50_is_not_accepted(): void
    {
        $this->fakeGateway(['data' => [], 'errors' => ['code' => -50, 'message' => 'Session is not valid, amounts values is not the same.']], 400);
        $authority = $this->startPayment();

        $order = $this->service()->handleCallback($authority, 'OK');

        $this->assertSame(OrderStatus::PaymentFailed, $order->status);
        $this->assertSame(PaymentStatus::Failed, Payment::sole()->status);
        $this->assertSame(5, $this->variant->fresh()->stock);
    }

    public function test_local_amount_mismatch_never_calls_verify(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();
        Payment::sole()->update(['amount' => 1000]); // tampered / inconsistent

        $order = $this->service()->handleCallback($authority, 'OK');

        Http::assertNotSent(fn (Request $r) => $r->url() === self::VERIFY_URL);
        $this->assertSame(OrderStatus::NeedsAttention, $order->status);
        $this->assertSame(5, $this->variant->fresh()->stock);
    }

    public function test_repeated_callback_is_idempotent(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();

        $this->service()->handleCallback($authority, 'OK');
        $this->service()->handleCallback($authority, 'OK');
        $this->service()->handleCallback($authority, 'NOK');

        Http::assertSentCount(2); // one request + one verify, no second verify
        $this->assertSame(3, $this->variant->fresh()->stock);
        $this->assertSame(OrderStatus::Paid, $this->order->fresh()->status);
        $this->assertSame(PaymentStatus::Paid, Payment::sole()->status);
        Queue::assertPushed(SendOrderConfirmationSms::class, 1);
    }

    public function test_already_verified_101_counts_once(): void
    {
        $this->fakeGateway(['data' => ['code' => 101, 'message' => 'Verified', 'ref_id' => 201], 'errors' => []]);
        $authority = $this->startPayment();

        $order = $this->service()->handleCallback($authority, 'OK');

        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertSame(3, $this->variant->fresh()->stock);
    }

    public function test_verify_connection_error_keeps_attempt_pending_and_can_retry(): void
    {
        Http::fakeSequence(self::REQUEST_URL)->push(['data' => ['code' => 100, 'authority' => 'S000000000000000000000000000000AUTH1'], 'errors' => []]);
        Http::fake([self::VERIFY_URL => Http::sequence()
            ->pushFailedConnection()
            ->push(['data' => ['code' => 100, 'ref_id' => 777], 'errors' => []])]);
        $authority = $this->startPayment();

        try {
            $this->service()->handleCallback($authority, 'OK');
            $this->fail('Expected PaymentException');
        } catch (PaymentException $e) {
            $this->assertStringContainsString('هنوز مشخص نیست', $e->getMessage());
        }
        $this->assertSame(PaymentStatus::Pending, Payment::sole()->status);
        $this->assertSame(5, $this->variant->fresh()->stock);

        $order = $this->service()->handleCallback($authority, 'OK');

        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertSame('777', Payment::sole()->ref_id);
    }

    public function test_paid_but_not_enough_stock_needs_attention(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();
        $this->variant->update(['stock' => 1]); // someone else bought meanwhile

        $order = $this->service()->handleCallback($authority, 'OK');

        $this->assertSame(OrderStatus::NeedsAttention, $order->status);
        $this->assertSame(PaymentStatus::Paid, Payment::sole()->status, 'The money record is kept.');
        $this->assertSame(1, $this->variant->fresh()->stock, 'Stock is not reduced.');
        $this->assertNotNull($order->paid_at);
    }

    public function test_payment_for_order_cancelled_meanwhile_needs_attention(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();
        $this->order->update(['status' => OrderStatus::Cancelled]);

        $order = $this->service()->handleCallback($authority, 'OK');

        $this->assertSame(OrderStatus::NeedsAttention, $order->status);
        $this->assertSame(5, $this->variant->fresh()->stock);
    }

    public function test_unknown_authority(): void
    {
        Http::fake();

        $this->expectException(PaymentException::class);
        $this->service()->handleCallback('S-UNKNOWN', 'OK');
    }

    public function test_merchant_id_is_never_logged(): void
    {
        Log::spy();
        $this->fakeGateway(['data' => [], 'errors' => ['code' => -51, 'message' => 'x']], 400);
        $authority = $this->startPayment();

        $this->service()->handleCallback($authority, 'OK');

        Log::shouldHaveReceived('warning')->withArgs(fn ($m, $context = []) => ! str_contains(json_encode($context), self::MERCHANT));
        $this->assertStringNotContainsString(self::MERCHANT, json_encode(Payment::sole()->payload));
    }

    // --- HTTP endpoints ---------------------------------------------------------------

    public function test_callback_route_redirects_owner_to_result(): void
    {
        $this->fakeGateway();
        $authority = $this->startPayment();

        $this->get('/payment/callback?Authority='.$authority.'&Status=OK')
            ->assertRedirect(route('payment.result', $this->order));

        $this->actingAs($this->user)->get(route('payment.result', $this->order))
            ->assertOk()
            ->assertSee('پرداخت با موفقیت انجام شد.')
            ->assertSee($this->order->number)
            ->assertSee('کد پیگیری بانک: ۲۰۱');
    }

    public function test_result_page_is_only_for_the_owner(): void
    {
        $this->actingAs(User::create(['mobile' => '09351234567']))
            ->get(route('payment.result', $this->order))
            ->assertNotFound();
    }

    public function test_callback_without_authority_is_404_and_unknown_shows_message(): void
    {
        Http::fake();

        $this->get('/payment/callback')->assertNotFound();
        $this->get('/payment/callback?Authority=S-UNKNOWN&Status=OK')->assertOk()->assertSee('اطلاعات این پرداخت پیدا نشد.');
    }

    public function test_sms_job_sends_order_confirmation(): void
    {
        $sms = new \Tests\Support\FakeSmsService;
        $this->app->instance(\App\Services\Sms\SmsService::class, $sms);

        (new SendOrderConfirmationSms($this->order->id))->handle($sms);

        $this->assertSame([['type' => 'order', 'mobile' => '09121234567', 'token' => $this->order->number]], $sms->sent);
    }
}
