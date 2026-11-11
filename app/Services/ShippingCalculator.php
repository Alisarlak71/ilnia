<?php

namespace App\Services;

use App\Models\ShippingMethod;

/**
 * The ONLY place shipping cost is calculated (docs/ARCHITECTURE.md).
 *
 * Rule (confirmed by the owner): if the order's total weight is greater than OR EQUAL to the
 * method's free_over_weight_grams, shipping is free. An empty threshold means never free.
 * Cost and threshold come from the database (editable in the admin panel), never from code.
 */
class ShippingCalculator
{
    /** First active shipping method (for now: «پست»), or null if none is active. */
    public function defaultMethod(): ?ShippingMethod
    {
        return ShippingMethod::query()->active()->first();
    }

    /** Shipping cost in Toman for an order of $weightGrams. */
    public function cost(ShippingMethod $method, int $weightGrams): int
    {
        return $this->isFree($method, $weightGrams) ? 0 : $method->cost;
    }

    public function isFree(ShippingMethod $method, int $weightGrams): bool
    {
        return $method->free_over_weight_grams !== null
            && $weightGrams >= $method->free_over_weight_grams;
    }

    /**
     * Grams still needed for free shipping: 0 when already free, null when the method
     * has no free-shipping threshold.
     */
    public function remainingForFree(ShippingMethod $method, int $weightGrams): ?int
    {
        if ($method->free_over_weight_grams === null) {
            return null;
        }

        return max(0, $method->free_over_weight_grams - $weightGrams);
    }
}
