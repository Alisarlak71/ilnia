<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test data only. Product names are labelled «نمونه» (sample).
 *
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => 'محصول نمونه '.fake()->unique()->numberBetween(1, 99999),
            'short_description' => 'توضیح کوتاه نمونه',
            'description' => 'توضیح نمونه برای تست.',
            'story' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
