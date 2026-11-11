<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Address;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Turns the customer's cart into an order (status: pending_payment).
 *
 * - Amounts come only from OrderPricing (same code as the checkout page), with prices read
 *   from the database by CartService — never from the browser.
 * - Address, product names, weights and prices are copied (snapshot) into the order.
 * - If the cart changed since the customer last saw it (item gone, stock dropped), NO order is
 *   created: the customer must review the cart first.
 * - Stock is NOT reduced here; it is reduced only after a verified payment (PaymentService,
 *   with a row lock), so unpaid orders never hold stock.
 * - The cart is emptied once the order is saved.
 */
class OrderService
{
    public const NOTE_MAX = 500;

    public function __construct(
        private readonly CartService $cart,
        private readonly OrderPricing $pricing,
    ) {
    }

    /** @throws OrderException */
    public function createFromCart(User $user, int $addressId, int $shippingMethodId, ?string $note = null): Order
    {
        $address = $user->addresses()->find($addressId);
        if (! $address instanceof Address) {
            throw new OrderException(__('order.invalid_address'));
        }

        $method = ShippingMethod::query()->active()->find($shippingMethodId);
        if (! $method instanceof ShippingMethod) {
            throw new OrderException(__('order.invalid_shipping'));
        }

        if ($this->cart->isEmpty()) {
            throw new OrderException(__('order.cart_empty'));
        }

        // lines() re-checks the cart against the database and corrects it if needed.
        // Any correction means the customer has not seen the final cart: stop here.
        $before = $this->cart->raw();
        $lines = $this->cart->lines();

        if ($this->cart->raw() !== $before) {
            throw new OrderException(__('order.cart_changed'));
        }

        if ($lines->isEmpty()) {
            throw new OrderException(__('order.cart_empty'));
        }

        $totals = $this->pricing->quote($lines, $method);
        $note = filled($note) ? mb_substr(trim($note), 0, self::NOTE_MAX) : null;

        $order = DB::transaction(function () use ($user, $address, $method, $lines, $totals, $note) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::PendingPayment,
                'subtotal' => $totals->subtotal,
                'shipping_cost' => $totals->shippingCost,
                'discount' => $totals->discount,
                'total' => $totals->total,
                'total_weight_grams' => $totals->weightGrams,
                'shipping_method_name' => $method->name,
                'recipient_name' => $address->recipient_name,
                'recipient_mobile' => $address->mobile,
                'province' => $address->province,
                'city' => $address->city,
                'address_line' => $address->address_line,
                'postal_code' => $address->postal_code,
                'customer_note' => $note,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_variant_id' => $line->variant->id,
                    'product_name' => $line->variant->product->name,
                    'variant_title' => $line->variant->title,
                    'weight_grams' => $line->variant->weight_grams,
                    'unit_price' => $line->unitPrice,
                    'quantity' => $line->quantity,
                    'line_total' => $line->lineTotal,
                ]);
            }

            return $order;
        });

        $this->cart->clear();

        return $order->load('items');
    }
}
