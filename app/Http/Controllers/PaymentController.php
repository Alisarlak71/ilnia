<?php

namespace App\Http\Controllers;

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
            return view('payment.result', ['order' => null, 'message' => $e->getMessage()]);
        }

        return redirect()->route('payment.result', $order);
    }

    /** Result page for the order's owner. (Final design: later roadmap step.) */
    public function result(Request $request, Order $order): View
    {
        abort_unless($order->user_id !== null && $order->user_id === $request->user()?->id, 404);

        // payment_error: the gateway could not be reached right after checkout.
        return view('payment.result', ['order' => $order->load('payments'), 'message' => session('payment_error')]);
    }
}
