<?php

namespace App\Http\Controllers\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "My orders" (/account/orders) and one order's details (/account/orders/{number}).
 * Every lookup goes through the logged-in customer's own orders.
 */
class OrderController extends Controller
{
    public const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->withCount('items')
            ->latest('id')
            ->paginate(self::PER_PAGE);

        return view('account.orders.index', ['orders' => $orders]);
    }

    public function show(Request $request, string $number, PaymentService $payments): View
    {
        $order = $request->user()->orders()->where('number', $number)->with(['items', 'payments'])->firstOrFail();

        return view('account.orders.show', [
            'order' => $order,
            'canRetry' => $payments->isEnabled()
                && in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::PaymentFailed], true),
        ]);
    }
}
