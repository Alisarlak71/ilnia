<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Test data only. @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway' => 'zarinpal',
            'amount' => 540000,
            'authority' => 'A'.fake()->unique()->numerify('###################################'),
            'status' => PaymentStatus::Pending,
        ];
    }
}
