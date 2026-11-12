<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_admin_pages_load(): void
    {
        $product = Product::factory()->create();

        $this->get('/admin/categories')->assertOk();
        $this->get('/admin/categories/create')->assertOk();
        $this->get('/admin/products')->assertOk();
        $this->get('/admin/products/create')->assertOk();
        $this->get("/admin/products/{$product->id}/edit")->assertOk();
    }

    public function test_non_admin_cannot_open_catalog_pages(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/products')->assertForbidden();
    }

    public function test_create_category(): void
    {
        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => 'عسل', 'sort' => '۲'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', ['name' => 'عسل', 'slug' => 'عسل', 'sort' => 2]);
    }

    public function test_category_with_products_has_no_delete_action(): void
    {
        $product = Product::factory()->create();
        $empty = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $product->category->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);

        Livewire::test(EditCategory::class, ['record' => $empty->getRouteKey()])
            ->assertActionVisible(DeleteAction::class);
    }

    public function test_categories_list_shows_records(): void
    {
        $categories = Category::factory()->count(2)->create();

        Livewire::test(ListCategories::class)->assertCanSeeTableRecords($categories);
    }

    public function test_create_product_with_variant_accepts_persian_digits(): void
    {
        $undoRepeaterFake = Repeater::fake();
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'عسل طبیعی',
                'category_id' => $category->id,
                'is_active' => true,
                'variants' => [
                    ['title' => '۱ کیلوگرم', 'weight_grams' => '۱۰۰۰', 'price' => '۴۵۰٬۰۰۰', 'stock' => '۱۲', 'is_active' => true],
                    ['title' => '۲ کیلوگرم', 'weight_grams' => '2,000', 'price' => '', 'stock' => '0', 'is_active' => true],
                ],
                'images' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $product = Product::where('name', 'عسل طبیعی')->firstOrFail();
        $this->assertSame('عسل-طبیعی', $product->slug);
        $this->assertTrue($product->is_active);

        $variants = $product->variants;
        $this->assertCount(2, $variants);
        $this->assertSame(1000, $variants[0]->weight_grams);
        $this->assertSame(450000, $variants[0]->price);
        $this->assertSame(12, $variants[0]->stock);
        $this->assertSame(2000, $variants[1]->weight_grams);
        $this->assertNull($variants[1]->price);
        $this->assertLessThan($variants[1]->sort, $variants[0]->sort);
    }

    public function test_decimal_price_is_rejected(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'عسل',
                'category_id' => Category::factory()->create()->id,
                'variants' => [
                    ['title' => '۱ کیلوگرم', 'weight_grams' => '1000', 'price' => '1500.5', 'stock' => '1', 'is_active' => true],
                ],
                'images' => [],
            ])
            ->call('create')
            ->assertHasFormErrors(['variants.0.price']);

        $undoRepeaterFake();

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_requires_name_and_category(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateProduct::class)
            ->fillForm(['name' => '', 'category_id' => null, 'variants' => [], 'images' => []])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required', 'category_id' => 'required']);

        $undoRepeaterFake();
    }

    public function test_edit_product_updates_variant_price(): void
    {
        $undoRepeaterFake = Repeater::fake();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create(['price' => 400000]);

        $component = Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()]);
        $component->assertFormSet(["variants.record-{$variant->id}.price" => '400,000']);

        $component
            ->fillForm([
                'variants' => [
                    ['title' => $variant->title, 'weight_grams' => '1000', 'price' => '۴۸۰,۰۰۰', 'stock' => '5', 'is_active' => true],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $this->assertSame(480000, $product->variants()->first()->price);
    }

    public function test_products_list_shows_records(): void
    {
        $products = Product::factory()->count(3)->create();

        Livewire::test(ListProducts::class)->assertCanSeeTableRecords($products);
    }

    public function test_deleting_image_removes_its_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/a.jpg', 'x');
        $image = ProductImage::factory()->create(['path' => 'products/a.jpg']);

        $image->delete();

        Storage::disk('public')->assertMissing('products/a.jpg');
    }

    public function test_replacing_image_file_removes_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/old.jpg', 'x');
        Storage::disk('public')->put('products/new.jpg', 'y');
        $image = ProductImage::factory()->create(['path' => 'products/old.jpg']);

        $image->update(['path' => 'products/new.jpg']);

        Storage::disk('public')->assertMissing('products/old.jpg');
        Storage::disk('public')->assertExists('products/new.jpg');
    }

    public function test_deleting_product_removes_image_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/b.jpg', 'x');
        $product = Product::factory()->create();
        ProductImage::factory()->for($product)->create(['path' => 'products/b.jpg']);

        $product->delete();

        Storage::disk('public')->assertMissing('products/b.jpg');
        $this->assertDatabaseCount('product_images', 0);
    }
}
