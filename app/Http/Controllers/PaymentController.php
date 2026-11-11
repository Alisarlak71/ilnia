<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\PaymentException;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Return from Zarinpal: /payment/callback?Authority=…&Status=OK|NOK
     * No login required (the session may have expired at the bank); the result is decided only by
     * server-side verify, and the result page itself is shown only to the order's owner.
     */
    public function callback(Request $request, PaymentService $payments): RedirectResponse|View
    {
        $authority = (string) $request->query('Authority', '');
        $status = (string) $request->query('Status', '');

        abort_if($authority === '' || strlen($authority) > 100, 404);

        try {
            $order = $payments->handleCallback($authority, $status);
        } catch (PaymentException $e) {
            return view('payment.result', ['order' => null, 'message' => $e->getMessage(), 'canRetry' => false]);
        }

        return redirect()->route('payment.result', $order);
    }

    /** Result page (success / failed / needs attention / not paid yet), for the order's owner only. */
    public function result(Request $request, Order $order, PaymentService $payments): View
    {
        $this->authorizeOwner($request, $order);

        return view('payment.result', [
            'order' => $order->load(['items', 'payments']),
            // payment_error: the gateway could not be reached (right after checkout or on retry).
            'message' => session('payment_error'),
            'canRetry' => $payments->isEnabled() && $this->isPayable($order),
        ]);
    }

    /**
     * "Pay again" for an unpaid order of the customer. The amount is the saved order total
     * (PaymentService); stock is checked first so nobody pays for something we no longer have.
     */
    public function retry(Request $request, Order $order, PaymentService $payments): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        if (! $this->isPayable($order)) {
            return redirect()->route('payment.result', $order);
        }

        if (! $this->stillInStock($order)) {
            return redirect()->route('payment.result', $order)->with('payment_error', __('payment.retry_out_of_stock'));
        }

        try {
            return redirect()->away($payments->request($order));
        } catch (PaymentException $e) {
            return redirect()->route('payment.result', $order)->with('payment_error', $e->getMessage());
        }
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id !== null && $order->user_id === $request->user()?->id, 404);
    }

    private function isPayable(Order $order): bool
    {
        return in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::PaymentFailed], true);
    }

    private function stillInStock(Order $order): bool
    {
        return $order->items()->with('variant.product')->get()->every(fn ($item) => $item->variant
            && $item->variant->is_active
            && $item->variant->product?->is_active
            && $item->variant->stock >= $item->quantity);
    }
}
