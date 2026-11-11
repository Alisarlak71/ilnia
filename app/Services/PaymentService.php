<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Jobs\SendOrderConfirmationSms;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Zarinpal payments (REST v4), called directly with Laravel's Http client.
 *
 *   $url   = $payments->request($order);                       // redirect the customer here
 *   $order = $payments->handleCallback($authority, $status);   // on return from the gateway
 *
 * Rules (AGENTS.md / docs/ARCHITECTURE.md):
 * - The amount always comes from the saved order (orders.total), never from the browser.
 * - Zarinpal amounts are in RIAL: Toman x 10. This is the ONLY place that converts.
 * - A payment counts only after server-side verify. Verify is idempotent: a repeated callback,
 *   or Zarinpal's "already verified" (101), never records anything twice.
 * - On success, stock is reduced inside one DB transaction with row locks. If stock is no longer
 *   enough, the order becomes needs_attention (the money must be returned by the admin).
 * - The merchant id is never logged.
 */
class PaymentService
{
    public const GATEWAY = 'zarinpal';

    private const CODE_SUCCESS = 100;

    private const CODE_ALREADY_VERIFIED = 101;

    public function isEnabled(): bool
    {
        return filled(config('services.zarinpal.merchant_id'));
    }

    /** Toman (our unit) → Rial (Zarinpal's default unit). */
    public static function toRial(int $toman): int
    {
        return $toman * 10;
    }

    /**
     * Start a payment attempt and return the gateway URL to redirect the customer to.
     *
     * @throws PaymentException
     */
    public function request(Order $order): string
    {
        if (! $this->isEnabled()) {
            throw new PaymentException(__('payment.disabled'));
        }

        if (! in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::PaymentFailed], true)) {
            throw new PaymentException(__('payment.not_payable'));
        }

        $payment = $order->payments()->create([
            'gateway' => self::GATEWAY,
            'amount' => $order->total,           // Toman, equals orders.total
            'status' => PaymentStatus::Pending,
        ]);

        if ($order->status === OrderStatus::PaymentFailed) {
            $order->update(['status' => OrderStatus::PendingPayment]);
        }

        try {
            $response = $this->post('/pg/v4/payment/request.json', [
                'merchant_id' => config('services.zarinpal.merchant_id'),
                'amount' => self::toRial($payment->amount),
                'currency' => 'IRR',
                'callback_url' => route('payment.callback'),
                'description' => 'سفارش '.$order->number.' از ایلنیا',
                'metadata' => array_filter([
                    'mobile' => $order->user?->mobile ?? $order->recipient_mobile,
                    'order_id' => $order->number,
                ]),
            ]);
        } catch (ConnectionException) {
            $this->failPayment($payment, ['error' => 'connection']);

            throw new PaymentException(__('payment.gateway_error'));
        }

        $authority = $response->json('data.authority');

        if ((int) $response->json('data.code') !== self::CODE_SUCCESS || blank($authority)) {
            $this->failPayment($payment, ['request' => $this->safeBody($response)]);
            Log::error('Zarinpal request failed', ['order' => $order->number, 'response' => $this->safeBody($response)]);

            throw new PaymentException(__('payment.gateway_error'));
        }

        $payment->update([
            'authority' => $authority,
            'payload' => ['request' => $this->safeBody($response)],
        ]);

        return $this->baseUrl().'/pg/StartPay/'.$authority;
    }

    /**
     * Handle the customer's return from Zarinpal (?Authority=…&Status=OK|NOK).
     * Safe to call any number of times for the same authority.
     *
     * @throws PaymentException
     */
    public function handleCallback(string $authority, string $status): Order
    {
        $payment = Payment::with('order')->where('authority', $authority)->first();

        if (! $payment || $payment->gateway !== self::GATEWAY) {
            throw new PaymentException(__('payment.not_found'));
        }

        $order = $payment->order;

        // Idempotent: this attempt has already been settled.
        if ($payment->status !== PaymentStatus::Pending) {
            return $order->fresh();
        }

        if (strtoupper($status) !== 'OK') {
            $this->cancelPayment($payment);

            return $order->fresh();
        }

        // Defence in depth: the amount sent to verify must be the saved order total.
        if ($payment->amount !== $order->total) {
            Log::critical('Payment amount differs from order total', ['order' => $order->number, 'payment' => $payment->id]);
            $this->failPayment($payment, ['error' => 'amount_mismatch_local'], OrderStatus::NeedsAttention);

            return $order->fresh();
        }

        try {
            $response = $this->post('/pg/v4/payment/verify.json', [
                'merchant_id' => config('services.zarinpal.merchant_id'),
                'amount' => self::toRial($payment->amount),
                'authority' => $authority,
            ]);
        } catch (ConnectionException) {
            // Result unknown: keep the attempt pending so it can be verified again later.
            Log::warning('Zarinpal verify: connection failed', ['order' => $order->number, 'payment' => $payment->id]);

            throw new PaymentException(__('payment.verify_unavailable'));
        }

        $code = (int) ($response->json('data.code') ?? $response->json('errors.code'));

        if (in_array($code, [self::CODE_SUCCESS, self::CODE_ALREADY_VERIFIED], true)) {
            return $this->markPaid($payment, (string) $response->json('data.ref_id'), $this->safeBody($response));
        }

        // -50 amount mismatch, -51 unsuccessful, -54 invalid authority, etc.
        Log::warning('Zarinpal verify failed', ['order' => $order->number, 'code' => $code]);
        $this->failPayment($payment, ['verify' => $this->safeBody($response)]);

        return $order->fresh();
    }

    /**
     * Record a verified payment: lock the rows, re-check, reduce stock, mark the order paid.
     * A second (parallel) callback finds the payment already paid and changes nothing.
     */
    private function markPaid(Payment $payment, string $refId, array $verifyBody): Order
    {
        $paidNow = DB::transaction(function () use ($payment, $refId, $verifyBody) {
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->first();
            $order = Order::whereKey($payment->order_id)->lockForUpdate()->first();

            if ($payment->status === PaymentStatus::Paid) {
                return false; // already recorded by another request
            }

            $payment->update([
                'status' => PaymentStatus::Paid,
                'ref_id' => $refId ?: null,
                'verified_at' => now(),
                'payload' => array_merge($payment->payload ?? [], ['verify' => $verifyBody]),
            ]);

            // Money arrived for an order that is no longer waiting for payment (e.g. cancelled by
            // the admin meanwhile): keep the money record, let the admin decide.
            if (! in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::PaymentFailed], true)) {
                Log::critical('Payment verified for an order that was not awaiting payment', ['order' => $order->number, 'status' => $order->status->value]);
                $order->update(['status' => OrderStatus::NeedsAttention, 'paid_at' => now()]);

                return true;
            }

            $items = $order->items()->get();
            $variants = ProductVariant::whereKey($items->pluck('product_variant_id')->filter())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $enoughStock = $items->every(fn ($item) => $item->product_variant_id !== null
                && ($variants->get($item->product_variant_id)?->stock ?? 0) >= $item->quantity);

            if (! $enoughStock) {
                Log::critical('Paid order without enough stock: money must be returned', ['order' => $order->number]);
                $order->update(['status' => OrderStatus::NeedsAttention, 'paid_at' => now()]);

                return true;
            }

            foreach ($items as $item) {
                $variants->get($item->product_variant_id)->decrement('stock', $item->quantity);
            }

            $order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);

            return true;
        });

        $order = $payment->order()->first();

        if ($paidNow) {
            SendOrderConfirmationSms::dispatch($order->id);
        }

        return $order;
    }

    private function cancelPayment(Payment $payment): void
    {
        $payment->update(['status' => PaymentStatus::Cancelled]);
        $this->markOrderFailed($payment->order);
    }

    private function failPayment(Payment $payment, array $payload, OrderStatus $orderStatus = OrderStatus::PaymentFailed): void
    {
        $payment->update([
            'status' => PaymentStatus::Failed,
            'payload' => array_merge($payment->payload ?? [], $payload),
        ]);

        if ($orderStatus === OrderStatus::NeedsAttention) {
            $payment->order->update(['status' => OrderStatus::NeedsAttention]);
        } else {
            $this->markOrderFailed($payment->order);
        }
    }

    /** Only an unpaid order may become payment_failed (never undo a paid order). */
    private function markOrderFailed(Order $order): void
    {
        if ($order->status === OrderStatus::PendingPayment) {
            $order->update(['status' => OrderStatus::PaymentFailed]);
        }
    }

    private function post(string $path, array $body): Response
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.zarinpal.timeout', 15))
            ->post($this->baseUrl().$path, $body);
    }

    private function baseUrl(): string
    {
        return rtrim((string) (config('services.zarinpal.sandbox')
            ? config('services.zarinpal.sandbox_url')
            : config('services.zarinpal.production_url')), '/');
    }

    /** Gateway response for storage/logs. Contains no merchant id (Zarinpal never echoes it). */
    private function safeBody(Response $response): array
    {
        return ['http_status' => $response->status(), 'body' => $response->json() ?? []];
    }
}
