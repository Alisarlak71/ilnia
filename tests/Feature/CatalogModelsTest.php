<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create();
        ProductImage::factory()->for($product)->create();

        $this->assertTrue($product->category->products->contains($product));
        $this->assertCount(1, $product->variants);
        $this->assertCount(1, $product->images);
        $this->assertTrue($product->variants->first()->product->is($product));
    }

    public function test_persian_slug_is_generated_from_name(): void
    {
        $category = Category::create(['name' => 'عسل']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'عسل عشایری ۱ کیلویی']);

        $this->assertSame('عسل', $category->slug);
        $this->assertSame('عسل-عشایری-1-کیلویی', $product->slug);
    }

    public function test_zwnj_is_removed_from_slug(): void
    {
        $category = Category::create(['name' => "عسل\u{200C}ها"]);

        $this->assertSame('عسلها', $category->slug);
    }

    public function test_duplicate_slugs_get_a_number(): void
    {
        $category = Category::factory()->create();
        $first = Product::create(['category_id' => $category->id, 'name' => 'عسل کوهستان']);
        $second = Product::create(['category_id' => $category->id, 'name' => 'عسل کوهستان']);

        $this->assertSame('عسل-کوهستان', $first->slug);
        $this->assertSame('عسل-کوهستان-2', $second->slug);
    }

    public function test_saving_again_keeps_the_same_slug(): void
    {
        $product = Product::factory()->create(['name' => 'عسل گون']);
        $product->update(['short_description' => 'تغییر']);

        $this->assertSame('عسل-گون', $product->fresh()->slug);
    }

    public function test_custom_slug_is_kept(): void
    {
        $product = Product::factory()->create(['name' => 'عسل', 'slug' => 'tribal-honey']);

        $this->assertSame('tribal-honey', $product->slug);
    }

    public function test_new_products_are_inactive_by_default(): void
    {
        $product = Product::create(['category_id' => Category::factory()->create()->id, 'name' => 'محصول تازه']);

        $this->assertFalse($product->fresh()->is_active);
    }

    public function test_active_scope_hides_inactive_products(): void
    {
        $active = Product::factory()->create();
        Product::factory()->inactive()->create();

        $this->assertEquals([$active->id], Product::active()->pluck('id')->all());
    }

    public function test_price_is_stored_as_integer_toman(): void
    {
        $variant = ProductVariant::factory()->create(['price' => 450000]);

        $this->assertSame(450000, $variant->fresh()->price);
    }

    public function test_variant_is_purchasable_only_when_active_priced_and_in_stock(): void
    {
        $this->assertTrue(ProductVariant::factory()->create()->isPurchasable());
        $this->assertFalse(ProductVariant::factory()->withoutPrice()->create()->isPurchasable());
        $this->assertFalse(ProductVariant::factory()->outOfStock()->create()->isPurchasable());
        $this->assertFalse(ProductVariant::factory()->inactive()->create()->isPurchasable());
        $this->assertFalse(ProductVariant::factory()->create(['price' => 0])->isPurchasable());
    }

    public function test_starting_price_is_lowest_active_priced_variant(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create(['price' => 900000, 'weight_grams' => 2000]);
        ProductVariant::factory()->for($product)->create(['price' => 480000, 'weight_grams' => 1000]);
        ProductVariant::factory()->for($product)->inactive()->create(['price' => 100000]);
        ProductVariant::factory()->for($product)->withoutPrice()->create();

        $this->assertSame(480000, $product->fresh()->startingPrice());
    }

    public function test_starting_price_is_null_without_priced_variants(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->withoutPrice()->create();

        $this->assertNull($product->fresh()->startingPrice());
    }

    public function test_variants_and_images_follow_sort_order(): void
    {
        $product = Product::factory()->create();
        $second = ProductVariant::factory()->for($product)->create(['sort' => 2]);
        $first = ProductVariant::factory()->for($product)->create(['sort' => 1]);
        $imgB = ProductImage::factory()->for($product)->create(['sort' => 5]);
        $imgA = ProductImage::factory()->for($product)->create(['sort' => 0]);

        $product = $product->fresh();
        $this->assertTrue($product->variants->first()->is($first));
        $this->assertTrue($product->mainImage()->is($imgA));
    }

    public function test_image_alt_falls_back_to_product_name(): void
    {
        $product = Product::factory()->create(['name' => 'عسل آویشن']);
        $withoutAlt = ProductImage::factory()->for($product)->create();
        $withAlt = ProductImage::factory()->for($product)->create(['alt' => 'شیشهٔ عسل روی میز']);

        $this->assertSame('عسل آویشن', $withoutAlt->altText());
        $this->assertSame('شیشهٔ عسل روی میز', $withAlt->altText());
    }

    public function test_deleting_product_deletes_its_variants_and_images(): void
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->for($product)->create();
        ProductImage::factory()->for($product)->create();

        $product->delete();

        $this->assertDatabaseCount('product_variants', 0);
        $this->assertDatabaseCount('product_images', 0);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->expectException(QueryException::class);
        $product->category->delete();
    }

    public function test_sku_must_be_unique(): void
    {
        ProductVariant::factory()->create(['sku' => 'HONEY-1KG']);

        $this->expectException(QueryException::class);
        ProductVariant::factory()->create(['sku' => 'HONEY-1KG']);
    }
}
