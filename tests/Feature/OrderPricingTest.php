<?php

namespace Tests\Feature;

use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_below_free_shipping_threshold(): void
    {
        $post = ShippingMethod::factory()->create(['cost' => 60000, 'free_over_weight_grams' => 4000]);
        $variant = ProductVariant::factory()->create(['price' => 480000, 'weight_grams' => 1000, 'stock' => 10]);
        app(CartService::class)->add($variant->id, 2);

        $totals = app(OrderPricing::class)->quote(app(CartService::class)->lines(), $post);

        $this->assertSame(960000, $totals->subtotal);
        $this->assertSame(60000, $totals->shippingCost);
        $this->assertSame(0, $totals->discount);
        $this->assertSame(1020000, $totals->total);
        $this->assertSame(2000, $totals->weightGrams);
    }

    public function test_quote_with_free_shipping(): void
    {
        $post = ShippingMethod::factory()->create(['cost' => 60000, 'free_over_weight_grams' => 4000]);
        $a = ProductVariant::factory()->create(['price' => 480000, 'weight_grams' => 1000, 'stock' => 10]);
        $b = ProductVariant::factory()->create(['price' => 930000, 'weight_grams' => 2000, 'stock' => 10]);
        app(CartService::class)->add($a->id, 2);
        app(CartService::class)->add($b->id, 1);

        $totals = app(OrderPricing::class)->quote(app(CartService::class)->lines(), $post);

        $this->assertSame(1890000, $totals->subtotal);
        $this->assertSame(0, $totals->shippingCost);
        $this->assertSame(1890000, $totals->total);
        $this->assertSame(4000, $totals->weightGrams);
    }
}
