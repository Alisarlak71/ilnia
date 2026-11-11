<?php

namespace Tests\Feature;

use App\Livewire\CartPage;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FreeShippingMessageTest extends TestCase
{
    use RefreshDatabase;

    private function addToCart(int $weightGrams, int $quantity = 1): ProductVariant
    {
        $variant = ProductVariant::factory()->create(['weight_grams' => $weightGrams, 'price' => 480000, 'stock' => 20]);
        app(CartService::class)->add($variant->id, $quantity);

        return $variant;
    }

    public function test_message_shows_remaining_kilograms(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);
        $this->addToCart(1000);

        $this->get('/cart')->assertSee('با افزودن ۳ کیلوگرم دیگر، ارسال رایگان می‌شود.');
    }

    public function test_fractional_kilograms(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);
        $this->addToCart(2500);

        $this->get('/cart')->assertSee('با افزودن ۱٫۵ کیلوگرم دیگر');
    }

    public function test_less_than_one_kilogram_is_shown_in_grams(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);
        $this->addToCart(3750);

        $this->get('/cart')->assertSee('با افزودن ۲۵۰ گرم دیگر، ارسال رایگان می‌شود.');
    }

    public function test_exactly_at_threshold_shows_free(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);
        $this->addToCart(1000, 4);

        $this->get('/cart')
            ->assertSee('ارسال این سفارش رایگان است.')
            ->assertDontSee('ارسال رایگان می‌شود');
    }

    public function test_message_follows_quantity_changes(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);
        $variant = $this->addToCart(1000, 3);

        Livewire::test(CartPage::class)
            ->assertSee('با افزودن ۱ کیلوگرم دیگر')
            ->call('increment', $variant->id)
            ->assertSee('ارسال این سفارش رایگان است.')
            ->call('decrement', $variant->id)
            ->assertSee('با افزودن ۱ کیلوگرم دیگر');
    }

    public function test_threshold_comes_from_the_panel(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 6000]);
        $this->addToCart(1000);

        $this->get('/cart')->assertSee('با افزودن ۵ کیلوگرم دیگر');
    }

    public function test_no_message_without_threshold_or_active_method(): void
    {
        $method = ShippingMethod::factory()->create(['free_over_weight_grams' => null]);
        $this->addToCart(1000);
        $this->get('/cart')->assertDontSee('ارسال رایگان');

        $method->update(['free_over_weight_grams' => 4000, 'is_active' => false]);
        $this->get('/cart')->assertDontSee('ارسال رایگان');
    }

    public function test_no_message_for_empty_cart(): void
    {
        ShippingMethod::factory()->create(['free_over_weight_grams' => 4000]);

        $this->get('/cart')->assertDontSee('ارسال رایگان');
    }
}
