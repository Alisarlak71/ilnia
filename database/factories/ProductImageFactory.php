<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test data only (no real file is created).
 *
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'products/sample-'.fake()->unique()->numberBetween(1, 99999).'.jpg',
            'alt' => null,
            'sort' => 0,
        ];
    }
}
