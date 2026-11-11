<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Test data only. @extends Factory<ShippingMethod> */
class ShippingMethodFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'پست', 'cost' => 60000, 'free_over_weight_grams' => 4000, 'is_active' => true, 'sort' => 0];
    }
}
