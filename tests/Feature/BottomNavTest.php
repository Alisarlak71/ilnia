<?php

namespace Tests\Feature;

use App\Livewire\CartIcon;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BottomNavTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_bottom_bar_has_home_products_and_cart(): void
    {
        $response = $this->get('/');

        $response->assertSee('aria-label="منوی پایین"', false);
        $response->assertSeeInOrder(['منوی پایین', 'خانه', 'محصولات', 'سبد خرید']);
        $response->assertSee('md:hidden', false);
    }

    public function test_bottom_bar_marks_the_current_page(): void
    {
        $html = $this->get('/products')->getContent();
        $nav = substr($html, strpos($html, 'aria-label="منوی پایین"'));

        $this->assertMatchesRegularExpression('#href="'.preg_quote(route('products.index'), '#').'"[^>]*\s+aria-current="page"#', $nav);
    }

    public function test_cart_tab_shows_live_count(): void
    {
        $component = Livewire::test(CartIcon::class, ['variant' => 'tab'])
            ->assertSee('سبد خرید، ۰ کالا');

        $variant = ProductVariant::factory()->create(['price' => 480000, 'stock' => 10]);
        app(CartService::class)->add($variant->id, 2);

        $component->dispatch('cart-updated')->assertSee('سبد خرید، ۲ کالا');
    }

    public function test_header_strip_shows_free_shipping_from_settings(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);

        $this->get('/')->assertSee('ارسال رایگان برای سفارش‌های ۴ کیلوگرم و بیشتر');
    }

    public function test_cart_icon_has_a_single_root_element_in_both_looks(): void
    {
        // An @if at the root makes Livewire nest the updated link inside the old one ("سبد خرید" twice).
        foreach (['header', 'tab'] as $variant) {
            $html = trim(Livewire::test(CartIcon::class, ['variant' => $variant])->html());

            $this->assertStringStartsWith('<div', $html, "variant {$variant}");
            $this->assertStringEndsWith('</div>', $html, "variant {$variant}");
        }
    }
}
