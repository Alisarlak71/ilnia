<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * All environments: structural data only (the «پست» shipping method).
     * Real products, prices and the admin user are created from the admin panel /
     * `php artisan ilnia:make-admin`, never with fake data.
     */
    public function run(): void
    {
        $this->call(ShippingMethodSeeder::class);

        // Sample catalog («نمونه») — local development only.
        if (app()->environment('local')) {
            $this->call(DevSampleSeeder::class);
        }
    }
}
