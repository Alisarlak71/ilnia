<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_loads_with_empty_state(): void
    {
        $this->get('/products')
            ->assertOk()
            ->assertSee('<title>محصولات | ایلنیا</title>', false)
            ->assertSee('هنوز محصولی اینجا نیست. به‌زودی اضافه می‌شود.');
    }

    public function test_lists_only_active_products_with_count(): void
    {
        Product::factory()->count(3)->create();
        Product::factory()->inactive()->create(['name' => 'محصول مخفی نمونه']);

        $this->get('/products')
            ->assertOk()
            ->assertSee('۳ محصول')
            ->assertDontSee('محصول مخفی نمونه');
    }

    public function test_cards_show_prices(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create(['price' => 480000]);

        $this->get('/products')->assertSee('از ۴۸۰٬۰۰۰ تومان');
    }

    public function test_filter_by_category(): void
    {
        $honey = Category::create(['name' => 'عسل']);
        $oil = Category::create(['name' => 'روغن']);
        Product::factory()->for($honey)->create(['name' => 'عسل نمونه الف']);
        Product::factory()->for($oil)->create(['name' => 'روغن نمونه ب']);

        $this->get('/products?category='.urlencode('عسل'))
            ->assertOk()
            ->assertSee('<title>عسل | ایلنیا</title>', false)
            ->assertSee('عسل نمونه الف')
            ->assertDontSee('روغن نمونه ب')
            ->assertSee('aria-current="page"', false);
    }

    public function test_category_buttons_only_for_categories_with_active_products(): void
    {
        $honey = Category::create(['name' => 'عسل']);
        $oil = Category::create(['name' => 'روغن']);
        $empty = Category::create(['name' => 'دستهٔ خالی']);
        $hiddenOnly = Category::create(['name' => 'فقط مخفی']);
        Product::factory()->for($honey)->create();
        Product::factory()->for($oil)->create();
        Product::factory()->for($hiddenOnly)->inactive()->create();

        $this->get('/products')
            ->assertSee('همهٔ محصولات')
            ->assertSee('روغن')
            ->assertDontSee('دستهٔ خالی')
            ->assertDontSee('فقط مخفی');
    }

    public function test_unknown_or_empty_category_returns_404(): void
    {
        $empty = Category::create(['name' => 'دستهٔ خالی']);

        $this->get('/products?category=ناموجود')->assertNotFound();
        $this->get('/products?category='.urlencode($empty->slug))->assertNotFound();
    }

    public function test_pagination_twelve_per_page(): void
    {
        Product::factory()->count(13)->create();

        $this->get('/products')
            ->assertSee('صفحهٔ ۱ از ۲')
            ->assertSee('rel="next"', false);

        $this->get('/products?page=2')
            ->assertOk()
            ->assertSee('صفحهٔ ۲ از ۲')
            ->assertSee('rel="prev"', false);
    }

    public function test_header_links_to_products_and_marks_current_page(): void
    {
        $this->get('/products')
            ->assertSee('href="'.route('products.index').'"', false)
            ->assertSee('aria-current="page"', false);
    }
}
