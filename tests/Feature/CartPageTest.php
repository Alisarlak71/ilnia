<?php

namespace Tests\Feature;

use App\Livewire\CartIcon;
use App\Livewire\CartPage;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    private function inCart(array $attributes = [], int $quantity = 1): ProductVariant
    {
        $variant = ProductVariant::factory()->create($attributes + ['price' => 480000, 'stock' => 10]);
        app(CartService::class)->add($variant->id, $quantity);

        return $variant;
    }

    public function test_empty_cart_page(): void
    {
        $this->get('/cart')
            ->assertOk()
            ->assertSee('<title>سبد خرید | ایلنیا</title>', false)
            ->assertSee('سبد خرید شما خالی است')
            ->assertSee(route('products.index'), false);
    }

    public function test_cart_page_shows_lines_and_subtotal(): void
    {
        $variant = $this->inCart(['title' => '۱ کیلوگرم', 'price' => 480000], 2);
        $this->inCart(['price' => 930000]);

        $this->get('/cart')
            ->assertOk()
            ->assertSee($variant->product->name)
            ->assertSee('قیمت واحد: ۴۸۰٬۰۰۰ تومان')
            ->assertSee('۹۶۰٬۰۰۰ تومان')
            ->assertSee('۱٬۸۹۰٬۰۰۰ تومان')
            ->assertSee('هزینهٔ ارسال در مرحلهٔ تسویه‌حساب محاسبه می‌شود.');
    }

    public function test_increment_decrement_and_remove(): void
    {
        $variant = $this->inCart(quantity: 2);

        $component = Livewire::test(CartPage::class)
            ->call('increment', $variant->id)
            ->assertDispatched('cart-updated');
        $this->assertSame(3, app(CartService::class)->count());

        $component->call('decrement', $variant->id);
        $this->assertSame(2, app(CartService::class)->count());

        $component->call('remove', $variant->id)->assertSee('سبد خرید شما خالی است');
        $this->assertTrue(app(CartService::class)->isEmpty());
    }

    public function test_decrement_to_zero_removes_item(): void
    {
        $variant = $this->inCart();

        Livewire::test(CartPage::class)->call('decrement', $variant->id);

        $this->assertTrue(app(CartService::class)->isEmpty());
    }

    public function test_increment_above_stock_shows_error(): void
    {
        $variant = $this->inCart(['stock' => 2], 2);

        Livewire::test(CartPage::class)
            ->call('increment', $variant->id)
            ->assertSee('فقط ۲ عدد موجود است');

        $this->assertSame(2, app(CartService::class)->count());
    }

    public function test_cannot_add_items_that_are_not_in_cart(): void
    {
        $this->inCart();
        $other = ProductVariant::factory()->create(['price' => 1000, 'stock' => 5]);

        Livewire::test(CartPage::class)->call('increment', $other->id);

        $this->assertArrayNotHasKey($other->id, app(CartService::class)->raw());
    }

    public function test_adjustment_messages_are_shown(): void
    {
        $variant = $this->inCart(['stock' => 10], 5);
        $variant->update(['stock' => 3]);

        $this->get('/cart')->assertSee('به ۳ (موجودی فعلی) کاهش یافت');
    }

    public function test_checkout_button_is_disabled_until_phase_three(): void
    {
        $this->inCart();

        $this->get('/cart')->assertSee('تسویه‌حساب و پرداخت آنلاین به‌زودی فعال می‌شود.');
    }

    public function test_cart_icon_shows_count_and_refreshes(): void
    {
        $component = Livewire::test(CartIcon::class)
            ->assertSee('سبد خرید، ۰ کالا')
            ->assertDontSeeHtml('rounded-full bg-gold');

        $this->inCart(quantity: 3);

        $component->dispatch('cart-updated')
            ->assertSee('سبد خرید، ۳ کالا')
            ->assertSeeHtml('rounded-full bg-gold');
    }

    public function test_header_has_cart_icon_on_every_page(): void
    {
        $this->get('/')->assertSee('href="'.route('cart').'"', false);
        $this->get('/products')->assertSee('href="'.route('cart').'"', false);
    }
}
