<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test data only.
 *
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'title' => '۱ کیلوگرم',
            'weight_grams' => 1000,
            'price' => fake()->numberBetween(100, 900) * 1000,
            'stock' => 10,
            'sku' => null,
            'is_active' => true,
            'sort' => 0,
        ];
    }

    public function withoutPrice(): static
    {
        return $this->state(fn () => ['price' => null]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
