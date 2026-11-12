<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_brand_sections(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('اصلش را می‌فروشیم، نه ادعایش را');
        $response->assertSee('اصالت');
        $response->assertSee('صداقت');
        $response->assertSee('لوکس');
        $response->assertSee('ایلنیا یعنی چه؟');
        $response->assertSee('به‌زودی محصولات ایلنیا اینجا قرار می‌گیرند.');
    }

    public function test_only_active_products_are_listed(): void
    {
        $active = Product::factory()->create(['name' => 'عسل فعال نمونه']);
        Product::factory()->inactive()->create(['name' => 'عسل مخفی نمونه']);

        $response = $this->get('/');

        $response->assertSee($active->name);
        $response->assertDontSee('عسل مخفی نمونه');
        $response->assertDontSee('به‌زودی محصولات ایلنیا اینجا قرار می‌گیرند.');
    }

    public function test_card_shows_starting_price_and_weights(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create(['title' => '۱ کیلوگرم', 'price' => 480000]);
        ProductVariant::factory()->for($product)->create(['title' => '۲ کیلوگرم', 'price' => 930000, 'weight_grams' => 2000]);

        $response = $this->get('/');

        $response->assertSee('از ۴۸۰٬۰۰۰ تومان');
        $response->assertSee('۱ کیلوگرم، ۲ کیلوگرم');
        $response->assertDontSee('ناموجود');
    }

    public function test_card_without_price_shows_price_soon(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->withoutPrice()->create();

        $this->get('/')->assertSee('قیمت به‌زودی')->assertDontSee('ناموجود');
    }

    public function test_card_out_of_stock_shows_badge(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->outOfStock()->create(['price' => 450000]);

        $this->get('/')->assertSee('ناموجود')->assertSee('از ۴۵۰٬۰۰۰ تومان');
    }

    public function test_inactive_variant_is_not_listed_on_card(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create(['title' => '۱ کیلوگرم', 'price' => 480000]);
        ProductVariant::factory()->for($product)->inactive()->create(['title' => '۵ کیلوگرم', 'price' => 100000]);

        $this->get('/')->assertDontSee('۵ کیلوگرم')->assertSee('از ۴۸۰٬۰۰۰ تومان');
    }

    public function test_placeholder_without_photo_and_real_photo_with_alt(): void
    {
        $withoutPhoto = Product::factory()->create();
        $withPhoto = Product::factory()->create(['name' => 'عسل با عکس نمونه']);
        ProductImage::factory()->for($withPhoto)->create(['path' => 'products/real.jpg']);

        $response = $this->get('/');

        $response->assertSee('عکس واقعی این محصول به‌زودی اضافه می‌شود');
        $response->assertSee('storage/products/real.jpg', false);
        $response->assertSee('alt="عسل با عکس نمونه"', false);
    }

    public function test_no_fake_trust_claims_on_home_page(): void
    {
        Product::factory()->create();
        $html = $this->get('/')->getContent();

        foreach (['QR', 'آزمایش', 'پرفروش', 'تضمین', 'گواهی', 'شناسنامه'] as $claim) {
            $this->assertStringNotContainsString($claim, $html, "Home page must not claim «{$claim}».");
        }
    }
}
