<?php

namespace App\Services;

use App\Models\ProductVariant;

/**
 * One row of the cart, built from the database (never from the browser).
 * Money is integer Toman.
 */
final readonly class CartLine
{
    public int $unitPrice;

    public int $lineTotal;

    public int $weightGrams;

    public function __construct(
        public ProductVariant $variant,
        public int $quantity,
    ) {
        $this->unitPrice = (int) $variant->price;
        $this->lineTotal = $this->unitPrice * $quantity;
        $this->weightGrams = $variant->weight_grams * $quantity;
    }
}
