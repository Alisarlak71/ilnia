<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test data only. Never used for production content.
 *
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'دسته نمونه '.fake()->unique()->numberBetween(1, 99999),
            'sort' => 0,
        ];
    }
}
