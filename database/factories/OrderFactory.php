<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Test data only. @extends Factory<Order> */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::PendingPayment,
            'subtotal' => 480000,
            'shipping_cost' => 60000,
            'discount' => 0,
            'total' => 540000,
            'total_weight_grams' => 1000,
            'shipping_method_name' => 'پست',
            'recipient_name' => 'گیرندهٔ نمونه',
            'recipient_mobile' => '09121234567',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'خیابان نمونه، پلاک ۱',
            'postal_code' => '1234567890',
        ];
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(fn () => ['status' => $status, 'paid_at' => $status->isPaid() ? now() : null]);
    }
}
