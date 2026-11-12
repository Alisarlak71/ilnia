<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_url_shows_branded_404(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee('<html lang="fa" dir="rtl">', false)
            ->assertSee('<title>این صفحه پیدا نشد | ایلنیا</title>', false)
            ->assertSee('۴۰۴')
            ->assertSee('دیدن محصولات')
            ->assertSee('<header', false)
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_hidden_product_shows_404_page(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound()->assertSee('این صفحه پیدا نشد');
    }

    public function test_customer_opening_admin_sees_403_page(): void
    {
        $this->actingAs(User::create(['mobile' => '09121234567']))
            ->get('/admin')
            ->assertForbidden()
            ->assertSee('اجازهٔ دیدن این صفحه را ندارید')
            ->assertSee('۴۰۳');
    }

    public function test_server_error_shows_standalone_page_without_details_or_database(): void
    {
        config(['app.debug' => false]);
        Route::get('/__boom', fn () => throw new RuntimeException('secret internal detail'))->middleware('web');

        DB::enableQueryLog();
        DB::flushQueryLog();

        $response = $this->get('/__boom');

        $response->assertStatus(500)
            ->assertSee('مشکلی پیش آمد')
            ->assertSee('۵۰۰')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertDontSee('secret internal detail')
            ->assertDontSee('/build/assets/', false)   // no Vite dependency
            ->assertDontSee('livewire', false);         // no Livewire dependency
        $this->assertSame([], DB::getQueryLog(), 'The 500 page must not touch the database.');
    }

    public function test_419_and_429_and_503_pages_render(): void
    {
        $this->assertStringContainsString('زمان این صفحه تمام شده', view('errors.419')->render());
        $this->assertStringContainsString('کمی صبر کنید', view('errors.429')->render());
        $this->assertStringContainsString('به‌زودی برمی‌گردیم', view('errors.503')->render());
    }
}
