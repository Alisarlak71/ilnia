<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

/**
 * Structural data for ALL environments (production too): the «پست» shipping method.
 * Initial values chosen by the owner: 60,000 Toman, free for orders of 4 kg or more.
 * firstOrCreate: if the admin has changed the values in the panel, they are NOT overwritten.
 */
class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::firstOrCreate(
            ['name' => 'پست'],
            ['cost' => 60000, 'free_over_weight_grams' => 4000, 'is_active' => true, 'sort' => 0],
        );
    }
}
