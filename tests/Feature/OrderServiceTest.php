<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderPricing;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Address $address;

    private ShippingMethod $post;

    private CartService $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['mobile' => '09121234567']);
        $this->address = Address::factory()->for($this->user)->create([
            'recipient_name' => 'علی نمونه', 'mobile' => '09351234567', 'province' => 'فارس',
            'city' => 'شیراز', 'address_line' => 'خیابان نمونه، پلاک ۱۲', 'postal_code' => '7134567890',
        ]);
        $this->post = ShippingMethod::factory()->create(['name' => 'پست', 'cost' => 60000, 'free_over_weight_grams' => 4000]);
        $this->cart = app(CartService::class);
    }

    private function variant(array $attributes = []): ProductVariant
    {
        $product = Product::factory()->create(['name' => 'عسل عشایری نمونه']);

        return ProductVariant::factory()->for($product)->create($attributes + [
            'title' => '۱ کیلوگرم', 'price' => 480000, 'weight_grams' => 1000, 'stock' => 10,
        ]);
    }

    private function place(?string $note = null): Order
    {
        return app(OrderService::class)->createFromCart($this->user, $this->address->id, $this->post->id, $note);
    }

    private function assertOrderError(string $message): void
    {
        try {
            $this->place();
            $this->fail("Expected OrderException: {$message}");
        } catch (OrderException $e) {
            $this->assertSame($message, $e->getMessage());
        }

        $this->assertSame(0, Order::count(), 'No order may be created.');
    }

    // --- amounts --------------------------------------------------------------

    public function test_amounts_below_free_shipping(): void
    {
        $this->cart->add($this->variant()->id, 2);

        $order = $this->place();

        $this->assertSame(960000, $order->subtotal);
        $this->assertSame(60000, $order->shipping_cost);
        $this->assertSame(0, $order->discount);
        $this->assertSame(1020000, $order->total);
        $this->assertSame(2000, $order->total_weight_grams);
    }

    public function test_free_shipping_at_four_kilograms(): void
    {
        $this->cart->add($this->variant()->id, 4);

        $order = $this->place();

        $this->assertSame(0, $order->shipping_cost);
        $this->assertSame(1920000, $order->total);
    }

    public function test_multiple_lines_and_total_equals_items_plus_shipping(): void
    {
        $a = $this->variant(['price' => 480000, 'weight_grams' => 1000]);
        $b = $this->variant(['title' => '۵۰۰ گرم', 'price' => 260000, 'weight_grams' => 500]);
        $this->cart->add($a->id, 1);
        $this->cart->add($b->id, 3);

        $order = $this->place();

        $this->assertCount(2, $order->items);
        $this->assertSame($order->items->sum('line_total'), $order->subtotal);
        $this->assertSame($order->subtotal + $order->shipping_cost - $order->discount, $order->total);
        $this->assertSame(480000 + 3 * 260000 + 60000, $order->total);
        $this->assertSame(2500, $order->total_weight_grams);
    }

    public function test_amounts_match_checkout_quote_exactly(): void
    {
        $this->cart->add($this->variant()->id, 3);
        $quote = app(OrderPricing::class)->quote($this->cart->lines(), $this->post);

        $order = $this->place();

        $this->assertSame($quote->subtotal, $order->subtotal);
        $this->assertSame($quote->shippingCost, $order->shipping_cost);
        $this->assertSame($quote->total, $order->total);
    }

    public function test_current_database_price_is_used(): void
    {
        $variant = $this->variant(['price' => 480000]);
        $this->cart->add($variant->id, 1);
        $variant->update(['price' => 500000]);

        $this->assertSame(560000, $this->place()->total);
    }

    // --- snapshot ---------------------------------------------------------------

    public function test_address_and_items_are_copied_into_the_order(): void
    {
        $variant = $this->variant();
        $this->cart->add($variant->id, 2);

        $order = $this->place('  لطفاً عصر تحویل شود  ');

        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame($this->user->id, $order->user_id);
        $this->assertSame('پست', $order->shipping_method_name);
        $this->assertSame('علی نمونه', $order->recipient_name);
        $this->assertSame('09351234567', $order->recipient_mobile);
        $this->assertSame('فارس', $order->province);
        $this->assertSame('7134567890', $order->postal_code);
        $this->assertSame('لطفاً عصر تحویل شود', $order->customer_note);
        $this->assertNull($order->paid_at);
        $this->assertMatchesRegularExpression('/^IL-\d{6}-\d{4}$/', $order->number);

        $item = $order->items->first();
        $this->assertSame($variant->id, $item->product_variant_id);
        $this->assertSame('عسل عشایری نمونه', $item->product_name);
        $this->assertSame('۱ کیلوگرم', $item->variant_title);
        $this->assertSame(1000, $item->weight_grams);
        $this->assertSame(480000, $item->unit_price);
        $this->assertSame(2, $item->quantity);
        $this->assertSame(960000, $item->line_total);
    }

    public function test_later_changes_do_not_change_the_order(): void
    {
        $variant = $this->variant();
        $this->cart->add($variant->id, 1);
        $order = $this->place();

        $variant->update(['price' => 999000, 'title' => 'تغییر']);
        $variant->product->update(['name' => 'نام تازه']);
        $this->address->update(['city' => 'کرج', 'province' => 'البرز']);
        $this->post->update(['cost' => 90000, 'name' => 'پست پیشتاز']);

        $order->refresh();
        $item = $order->items()->first();
        $this->assertSame(540000, $order->total);
        $this->assertSame('شیراز', $order->city);
        $this->assertSame('پست', $order->shipping_method_name);
        $this->assertSame('عسل عشایری نمونه', $item->product_name);
        $this->assertSame(480000, $item->unit_price);
    }

    public function test_long_note_is_trimmed(): void
    {
        $this->cart->add($this->variant()->id, 1);

        $order = $this->place(str_repeat('ا', 600));

        $this->assertSame(OrderService::NOTE_MAX, mb_strlen($order->customer_note));
    }

    // --- stock & cart -------------------------------------------------------------

    public function test_stock_is_not_reduced_when_order_is_created(): void
    {
        $variant = $this->variant(['stock' => 5]);
        $this->cart->add($variant->id, 3);

        $this->place();

        $this->assertSame(5, $variant->fresh()->stock);
    }

    public function test_cart_is_emptied_after_order(): void
    {
        $this->cart->add($this->variant()->id, 1);

        $this->place();

        $this->assertTrue($this->cart->isEmpty());
    }

    public function test_no_order_when_stock_dropped_and_cart_shows_why(): void
    {
        $variant = $this->variant(['stock' => 10]);
        $this->cart->add($variant->id, 5);
        $variant->update(['stock' => 2]);

        $this->assertOrderError('موجودی یا قیمت بعضی از محصولات سبد شما تغییر کرده است. لطفاً سبد را دوباره بررسی کنید.');

        // Cart corrected (not emptied), and the reason is kept for the cart page.
        $this->assertSame([$variant->id => 2], $this->cart->raw());
        $this->assertStringContainsString('کاهش یافت', $this->cart->pullAdjustments()[0]);
    }

    public function test_no_order_when_a_product_became_unavailable(): void
    {
        $keep = $this->variant();
        $gone = $this->variant();
        $this->cart->add($keep->id, 1);
        $this->cart->add($gone->id, 1);
        $gone->product->update(['is_active' => false]);

        $this->assertOrderError('موجودی یا قیمت بعضی از محصولات سبد شما تغییر کرده است. لطفاً سبد را دوباره بررسی کنید.');
        $this->assertSame([$keep->id => 1], $this->cart->raw());
    }

    public function test_no_order_for_empty_cart(): void
    {
        $this->assertOrderError('سبد خرید شما خالی است.');
    }

    public function test_no_order_with_someone_elses_address(): void
    {
        $this->cart->add($this->variant()->id, 1);
        $this->address = Address::factory()->create(); // belongs to another user

        $this->assertOrderError('آدرس انتخاب‌شده معتبر نیست. یک آدرس انتخاب کنید.');
        $this->assertFalse($this->cart->isEmpty(), 'The cart stays when no order is created.');
    }

    public function test_no_order_with_inactive_shipping_method(): void
    {
        $this->cart->add($this->variant()->id, 1);
        $this->post->update(['is_active' => false]);

        $this->assertOrderError('روش ارسال انتخاب‌شده در دسترس نیست.');
    }
}
