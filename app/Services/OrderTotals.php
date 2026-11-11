<?php

namespace App\Services;

/**
 * Order amounts in integer Toman. total = subtotal + shippingCost - discount.
 */
final readonly class OrderTotals
{
    public int $total;

    public function __construct(
        public int $subtotal,
        public int $shippingCost,
        public int $discount,
        public int $weightGrams,
    ) {
        $this->total = max(0, $subtotal + $shippingCost - $discount);
    }
}
