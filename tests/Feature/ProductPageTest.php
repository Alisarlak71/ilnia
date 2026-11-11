<?php

namespace Tests\Feature;

use App\Livewire\ProductPurchase;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    private function honey(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'عسل']);

        return Product::factory()->for($category)->create($attributes + [
            'name' => 'عسل عشایری نمونه',
            'short_description' => 'توضیح کوتاه نمونه',
            'description' => "خط اول\nخط دوم",
            'story' => 'داستان نمونه',
            'storage_instructions' => 'نگهداری نمونه',
        ]);
    }

    public function test_page_opens_with_persian_slug(): void
    {
        $product = $this->honey();

        $this->get('/products/'.rawurlencode('عسل-عشایری-نمونه'))
            ->assertOk()
            ->assertSee('<title>عسل عشایری نمونه | ایلنیا</title>', false)
            ->assertSee('<h1', false);

        $this->assertSame(route('products.show', $product), url('/products/'.rawurlencode($product->slug)));
    }

    public function test_hidden_or_unknown_product_returns_404(): void
    {
        $hidden = Product::factory()->inactive()->create();

        $this->get(route('products.show', $hidden))->assertNotFound();
        $this->get('/products/'.rawurlencode('وجود-ندارد'))->assertNotFound();
    }

    public function test_shows_sections_breadcrumb_and_escapes_text(): void
    {
        $product = $this->honey(['description' => "خط اول\n<script>alert(1)</script>"]);

        $this->get(route('products.show', $product))
            ->assertSee('توضیح کوتاه نمونه')
            ->assertSee('توضیحات')
            ->assertSee('خط اول<br />', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('از کجا می‌آید؟')
            ->assertSee('داستان نمونه')
            ->assertSee('شرایط نگهداری')
            ->assertSee('نگهداری نمونه')
            ->assertSee(route('products.index', ['category' => $product->category->slug]), false);
    }

    public function test_empty_sections_are_hidden(): void
    {
        $product = $this->honey(['story' => null, 'storage_instructions' => null]);

        $this->get(route('products.show', $product))
            ->assertDontSee('از کجا می‌آید؟')
            ->assertDontSee('شرایط نگهداری');
    }

    public function test_placeholder_without_images_and_gallery_with_images(): void
    {
        $product = $this->honey();
        $this->get(route('products.show', $product))->assertSee('عکس واقعی این محصول به‌زودی اضافه می‌شود');

        ProductImage::factory()->for($product)->create(['path' => 'products/a.jpg', 'alt' => 'عکس اول']);
        ProductImage::factory()->for($product)->create(['path' => 'products/b.jpg', 'sort' => 1]);

        $this->get(route('products.show', $product))
            ->assertSee('alt="عکس اول"', false)
            ->assertSee('نمایش عکس ۲');
    }

    public function test_purchase_box_defaults_to_first_purchasable_weight(): void
    {
        $product = $this->honey();
        ProductVariant::factory()->for($product)->outOfStock()->create(['title' => '۵۰۰ گرم', 'price' => 260000, 'sort' => 0]);
        $kilo = ProductVariant::factory()->for($product)->create(['title' => '۱ کیلوگرم', 'price' => 480000, 'sort' => 1]);

        Livewire::test(ProductPurchase::class, ['product' => $product])
            ->assertSet('variantId', $kilo->id)
            ->assertSee('۴۸۰٬۰۰۰ تومان')
            ->assertSee('۵۰۰ گرم')
            ->assertSee('(ناموجود)');
    }

    public function test_choosing_a_weight_updates_the_price(): void
    {
        $product = $this->honey();
        ProductVariant::factory()->for($product)->create(['title' => '۱ کیلوگرم', 'price' => 480000]);
        $two = ProductVariant::factory()->for($product)->create(['title' => '۲ کیلوگرم', 'price' => 930000, 'weight_grams' => 2000, 'sort' => 1]);

        Livewire::test(ProductPurchase::class, ['product' => $product])
            ->set('variantId', $two->id)
            ->assertSet('variantId', $two->id)
            ->assertSee('۹۳۰٬۰۰۰ تومان');
    }

    public function test_variant_of_another_product_is_ignored(): void
    {
        $product = $this->honey();
        $own = ProductVariant::factory()->for($product)->create(['price' => 480000]);
        $other = ProductVariant::factory()->create(['price' => 1000]);
        $inactive = ProductVariant::factory()->for($product)->inactive()->create(['price' => 2000]);

        Livewire::test(ProductPurchase::class, ['product' => $product])
            ->set('variantId', $other->id)
            ->assertSet('variantId', $own->id)
            ->set('variantId', $inactive->id)
            ->assertSet('variantId', $own->id)
            ->assertDontSee('۱٬۰۰۰ تومان');
    }

    public function test_product_id_cannot_be_changed_from_the_browser(): void
    {
        $product = $this->honey();
        ProductVariant::factory()->for($product)->create();

        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::test(ProductPurchase::class, ['product' => $product])
            ->set('productId', Product::factory()->create()->id);
    }

    public function test_weight_without_price_shows_price_soon(): void
    {
        $product = $this->honey();
        ProductVariant::factory()->for($product)->withoutPrice()->create();

        Livewire::test(ProductPurchase::class, ['product' => $product])->assertSee('قیمت به‌زودی');
    }

    public function test_cart_button_is_disabled_until_phase_two(): void
    {
        $product = $this->honey();
        ProductVariant::factory()->for($product)->create();

        Livewire::test(ProductPurchase::class, ['product' => $product])
            ->assertSee('افزودن به سبد خرید')
            ->assertSee('خرید آنلاین به‌زودی فعال می‌شود.')
            ->assertSeeHtml('disabled');
    }

    public function test_cards_link_to_product_page(): void
    {
        $product = $this->honey();

        $this->get('/products')->assertSee('href="'.route('products.show', $product).'"', false);
        $this->get('/')->assertSee('href="'.route('products.show', $product).'"', false);
    }
}
