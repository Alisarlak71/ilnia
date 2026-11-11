<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Cancels old unpaid orders (pending_payment / payment_failed) that have had no payment
 * attempt for config('shop.unpaid_order_hours') hours. Runs every hour from the scheduler.
 *
 * Money safety: if an order still has a payment that went to the gateway but was never settled
 * (customer closed the tab, or verify could not reach Zarinpal), that payment is verified once
 * with the gateway first. If Zarinpal says it was paid, the order is recorded as paid (normal
 * flow, stock reduced) and NOT cancelled. If the gateway cannot be reached, the order is left
 * for the next run. Stock is never touched here: unpaid orders never hold stock.
 */
#[Signature('ilnia:cancel-unpaid-orders')]
#[Description('Cancel old unpaid orders (after checking unsettled payments with the gateway)')]
class CancelUnpaidOrdersCommand extends Command
{
    public function handle(PaymentService $payments): int
    {
        $hours = max(1, (int) config('shop.unpaid_order_hours', 24));
        $cutoff = now()->subHours($hours);
        $cancelled = $kept = 0;

        $orders = Order::query()
            ->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::PaymentFailed])
            ->where('created_at', '<', $cutoff)
            ->whereDoesntHave('payments', fn ($q) => $q->where('created_at', '>=', $cutoff))
            ->with('payments')
            ->lazyById(100);

        foreach ($orders as $order) {
            if (! $this->settleOpenPayments($order, $payments)) {
                $kept++;

                continue;
            }

            if ($this->cancel($order)) {
                $cancelled++;
            }
        }

        $this->info("Cancelled: {$cancelled}, left for later: {$kept}");

        return self::SUCCESS;
    }

    /** Verify every unsettled gateway payment. False = result unknown, do not cancel yet. */
    private function settleOpenPayments(Order $order, PaymentService $payments): bool
    {
        $open = $order->payments->where('status', PaymentStatus::Pending);

        foreach ($open as $payment) {
            if (blank($payment->authority)) {
                // Never reached the gateway: nothing to verify.
                $payment->update(['status' => PaymentStatus::Failed]);

                continue;
            }

            if (! $payments->isEnabled()) {
                return false; // cannot ask the gateway; leave it for the admin / a later run
            }

            try {
                $payments->handleCallback($payment->authority, 'OK');
            } catch (PaymentException) {
                Log::warning('Unpaid-order cleanup: gateway unreachable, order kept', ['order' => $order->number]);

                return false;
            }
        }

        return true;
    }

    /** Cancel only if the order is still unpaid (re-checked under a row lock). */
    private function cancel(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $fresh = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! in_array($fresh->status, [OrderStatus::PendingPayment, OrderStatus::PaymentFailed], true)) {
                return false; // paid meanwhile (e.g. by the verify above)
            }

            $fresh->update(['status' => OrderStatus::Cancelled]);

            return true;
        });
    }
}
