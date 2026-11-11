<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Production: nothing yet. Real products, prices and the admin user are created
     * from the admin panel / `php artisan ilnia:make-admin`, never with fake data.
     * (The shipping method «پست» is added here in Phase 3.)
     */
    public function run(): void
    {
        // Sample catalog («نمونه») — local development only.
        if (app()->environment('local')) {
            $this->call(DevSampleSeeder::class);
        }
    }
}
