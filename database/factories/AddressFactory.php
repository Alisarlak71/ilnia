<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test data only.
 *
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_name' => 'گیرندهٔ نمونه',
            'mobile' => '09121234567',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'خیابان نمونه، پلاک ۱',
            'postal_code' => '1234567890',
        ];
    }
}
