<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Test data only. @extends Factory<OrderItem> */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_variant_id' => null,
            'product_name' => 'محصول نمونه',
            'variant_title' => '۱ کیلوگرم',
            'weight_grams' => 1000,
            'unit_price' => 480000,
            'quantity' => 1,
            'line_total' => 480000,
        ];
    }
}
