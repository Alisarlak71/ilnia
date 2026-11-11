<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DevSampleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class DevSampleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_sample_catalog_covering_all_states(): void
    {
        $this->seed(DevSampleSeeder::class);

        $this->assertSame(2, Category::count());
        $this->assertSame(5, Product::count());
        $this->assertSame(4, Product::active()->count());

        $twoWeights = Product::where('slug', 'نمونه-عسل-عشایری')->firstOrFail();
        $this->assertCount(2, $twoWeights->variants);
        $this->assertSame(480000, $twoWeights->startingPrice());

        $this->assertFalse(Product::where('slug', 'نمونه-عسل-ناموجود')->firstOrFail()->variants->first()->isPurchasable());
        $this->assertNull(Product::where('slug', 'نمونه-بدون-قیمت')->firstOrFail()->startingPrice());
        $this->assertFalse(Product::where('slug', 'نمونه-مخفی')->firstOrFail()->is_active);
    }

    public function test_every_sample_is_labelled_namoone(): void
    {
        $this->seed(DevSampleSeeder::class);

        foreach (Category::all() as $category) {
            $this->assertStringContainsString('نمونه', $category->name);
        }

        foreach (Product::all() as $product) {
            $this->assertStringContainsString('نمونه', $product->name);
            $this->assertStringContainsString('نمونه', $product->description);
            $this->assertStringContainsString('نمونه', $product->story);
        }
    }

    public function test_no_images_and_no_users_are_created(): void
    {
        $this->seed(DevSampleSeeder::class);

        $this->assertDatabaseCount('product_images', 0);
        $this->assertSame(0, User::count());
    }

    public function test_running_twice_does_not_duplicate(): void
    {
        $this->seed(DevSampleSeeder::class);
        $this->seed(DevSampleSeeder::class);

        $this->assertSame(2, Category::count());
        $this->assertSame(5, Product::count());
        $this->assertSame(6, ProductVariant::count());
    }

    public function test_refuses_to_run_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->expectException(RuntimeException::class);

        (new DevSampleSeeder)->run();
    }

    public function test_database_seeder_adds_no_samples_outside_local(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])->assertSuccessful();

        $this->assertSame(0, Product::count());
        $this->assertSame(0, User::count());
    }
}
