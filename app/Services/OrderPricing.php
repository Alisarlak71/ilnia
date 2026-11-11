<?php

namespace App\Services;

use App\Models\ShippingMethod;
use Illuminate\Support\Collection;

/**
 * The single place where an order's money is calculated, so the amount the customer sees
 * at checkout and the amount saved by OrderService are always computed the same way.
 * Inputs are CartLine objects (prices already read from the database by CartService).
 */
class OrderPricing
{
    public function __construct(private readonly ShippingCalculator $shipping)
    {
    }

    /** @param  Collection<int, CartLine>  $lines */
    public function quote(Collection $lines, ShippingMethod $method): OrderTotals
    {
        $subtotal = (int) $lines->sum('lineTotal');
        $weight = (int) $lines->sum('weightGrams');

        return new OrderTotals(
            subtotal: $subtotal,
            shippingCost: $this->shipping->cost($method, $weight),
            discount: 0, // discount codes: later (see ROADMAP «بعداً»)
            weightGrams: $weight,
        );
    }
}
