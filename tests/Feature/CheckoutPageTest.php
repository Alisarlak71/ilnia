<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Livewire\CheckoutPage;
use App\Models\Order;
use App\Models\Address;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ShippingMethod $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['mobile' => '09121234567', 'name' => 'مشتری نمونه']);
        $this->post = ShippingMethod::factory()->create(['name' => 'پست', 'cost' => 60000, 'free_over_weight_grams' => 4000]);
    }

    private function fillCart(int $quantity = 2, int $weight = 1000, int $price = 480000): ProductVariant
    {
        $variant = ProductVariant::factory()->create(['price' => $price, 'weight_grams' => $weight, 'stock' => 20]);
        app(CartService::class)->add($variant->id, $quantity);

        return $variant;
    }

    public function test_guest_is_sent_to_login_and_back_to_checkout(): void
    {
        $this->fillCart();

        $this->get('/checkout')->assertRedirect(route('login'));
        $this->assertSame(url('/checkout'), session('url.intended'));
    }

    public function test_empty_cart_redirects_to_cart(): void
    {
        $this->actingAs($this->user);

        Livewire::test(CheckoutPage::class)->assertRedirect(route('cart'));
    }

    public function test_page_shows_summary_and_totals(): void
    {
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $variant = $this->fillCart(2);

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('<title>تسویه‌حساب | ایلنیا</title>', false)
            ->assertSee($variant->product->name)
            ->assertSee('۹۶۰٬۰۰۰ تومان')     // subtotal
            ->assertSee('۶۰٬۰۰۰ تومان')      // shipping
            ->assertSee('۱٬۰۲۰٬۰۰۰ تومان')   // total
            ->assertSee('وزن کل: ۲ کیلوگرم');
    }

    public function test_free_shipping_at_threshold(): void
    {
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $this->fillCart(4);

        $this->get('/checkout')
            ->assertSee('رایگان')
            ->assertSee('۱٬۹۲۰٬۰۰۰ تومان');
    }

    public function test_latest_address_and_default_method_are_preselected(): void
    {
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $latest = Address::factory()->for($this->user)->create();
        $this->fillCart();

        Livewire::test(CheckoutPage::class)
            ->assertSet('addressId', $latest->id)
            ->assertSet('shippingMethodId', $this->post->id)
            ->assertSet('addingAddress', false);
    }

    public function test_without_addresses_the_form_opens_prefilled(): void
    {
        $this->actingAs($this->user);
        $this->fillCart();

        Livewire::test(CheckoutPage::class)
            ->assertSet('addingAddress', true)
            ->assertSet('form.mobile', '09121234567')
            ->assertSee('هنوز آدرسی ثبت نکرده‌اید');
    }

    public function test_adding_an_address_saves_and_selects_it(): void
    {
        $this->actingAs($this->user);
        $this->fillCart();

        Livewire::test(CheckoutPage::class)
            ->set('form.recipient_name', 'گیرندهٔ نمونه')
            ->set('form.province', 'فارس')
            ->set('form.city', 'شیراز')
            ->set('form.address_line', 'خیابان نمونه، کوچهٔ ۳، پلاک ۱۲')
            ->set('form.postal_code', '۷۱۳۴۵۶۷۸۹۰')
            ->call('saveAddress')
            ->assertHasNoErrors()
            ->assertSet('addingAddress', false)
            ->assertSet('addressId', $this->user->addresses()->value('id'));

        $this->assertSame('7134567890', $this->user->addresses()->sole()->postal_code);
    }

    public function test_address_validation_errors_are_shown(): void
    {
        $this->actingAs($this->user);
        $this->fillCart();

        Livewire::test(CheckoutPage::class)
            ->set('form.postal_code', '123')
            ->call('saveAddress')
            ->assertHasErrors(['form.recipient_name' => [], 'form.postal_code' => []])
            ->assertSee('کد پستی باید 10 رقم باشد.');

        $this->assertSame(0, Address::count());
    }

    public function test_others_address_cannot_be_selected(): void
    {
        $this->actingAs($this->user);
        $mine = Address::factory()->for($this->user)->create(['address_line' => 'نشانی من، پلاک ۱']);
        $others = Address::factory()->create(['address_line' => 'نشانی مشتری دیگر']);
        $this->fillCart();

        $component = Livewire::test(CheckoutPage::class)->set('addressId', $others->id);

        $this->assertNull($component->instance()->selectedAddress);
        $component->assertDontSee('نشانی مشتری دیگر');
        $this->assertNotNull($mine);
    }

    public function test_inactive_shipping_method_cannot_be_selected(): void
    {
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $inactive = ShippingMethod::factory()->create(['name' => 'پیک', 'is_active' => false]);
        $this->fillCart();

        $component = Livewire::test(CheckoutPage::class)->set('shippingMethodId', $inactive->id);

        $this->assertNull($component->instance()->selectedMethod);
        $component->assertDontSee('پیک');
    }

    public function test_no_active_shipping_method_shows_message(): void
    {
        $this->actingAs($this->user);
        $this->post->update(['is_active' => false]);
        $this->fillCart();

        $this->get('/checkout')->assertSee('در حال حاضر امکان ارسال وجود ندارد');
    }

    public function test_prices_come_from_database_not_session(): void
    {
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $variant = $this->fillCart(1, price: 480000);
        $variant->update(['price' => 500000]);

        $this->get('/checkout')->assertSee('۵۶۰٬۰۰۰ تومان'); // 500,000 + 60,000
    }

    public function test_pay_button_is_disabled_until_payment_is_connected(): void
    {
        $this->actingAs($this->user);
        $this->fillCart();

        $this->get('/checkout')->assertSee('پرداخت آنلاین به‌زودی فعال می‌شود.');
    }

    // --- pay ------------------------------------------------------------------------

    private const REQUEST_URL = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';

    private function enableGateway(): void
    {
        config(['services.zarinpal.merchant_id' => '00000000-0000-0000-0000-000000000000', 'services.zarinpal.sandbox' => true]);
    }

    public function test_disabled_gateway_creates_no_order_even_if_pay_is_called(): void
    {
        Http::fake();
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $this->fillCart();

        Livewire::test(CheckoutPage::class)
            ->assertDontSeeHtml('wire:click="pay"')
            ->call('pay')
            ->assertHasErrors('pay')
            ->assertNoRedirect();

        $this->assertSame(0, Order::count());
        $this->assertFalse(app(CartService::class)->isEmpty(), 'The cart is kept.');
        Http::assertNothingSent();
    }

    public function test_enabled_gateway_shows_active_button(): void
    {
        $this->enableGateway();
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $this->fillCart();

        $this->get('/checkout')
            ->assertSeeHtml('wire:click="pay"')
            ->assertDontSee('پرداخت آنلاین به‌زودی فعال می‌شود.');
    }

    public function test_pay_creates_order_and_redirects_to_gateway(): void
    {
        $this->enableGateway();
        Http::fake([self::REQUEST_URL => Http::response(['data' => ['code' => 100, 'authority' => 'S000000000000000000000000000000AUTH1'], 'errors' => []])]);
        $this->actingAs($this->user);
        $address = Address::factory()->for($this->user)->create();
        $variant = $this->fillCart(2);

        Livewire::test(CheckoutPage::class)
            ->set('note', 'عصر تحویل شود')
            ->call('pay')
            ->assertRedirect('https://sandbox.zarinpal.com/pg/StartPay/S000000000000000000000000000000AUTH1');

        $order = Order::sole();
        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame(1020000, $order->total);
        $this->assertSame($address->postal_code, $order->postal_code);
        $this->assertSame('عصر تحویل شود', $order->customer_note);
        $this->assertSame(20, $variant->fresh()->stock, 'Stock is reduced only after payment.');
        $this->assertTrue(app(CartService::class)->isEmpty());
        $this->assertSame(PaymentStatus::Pending, $order->payments()->sole()->status);
        Http::assertSent(fn (Request $r) => $r['amount'] === 10200000); // rial, from the saved order
    }

    public function test_pay_without_address_creates_no_order(): void
    {
        $this->enableGateway();
        Http::fake();
        $this->actingAs($this->user);
        $this->fillCart();

        Livewire::test(CheckoutPage::class)->call('pay')->assertHasErrors('pay');

        $this->assertSame(0, Order::count());
        Http::assertNothingSent();
    }

    public function test_pay_with_someone_elses_address_creates_no_order(): void
    {
        $this->enableGateway();
        Http::fake();
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $other = Address::factory()->create();
        $this->fillCart();

        Livewire::test(CheckoutPage::class)->set('addressId', $other->id)->call('pay')->assertHasErrors('pay');

        $this->assertSame(0, Order::count());
    }

    public function test_changed_cart_sends_customer_back_to_cart_with_reason(): void
    {
        $this->enableGateway();
        Http::fake();
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $variant = $this->fillCart(5);

        $component = Livewire::test(CheckoutPage::class);
        $variant->update(['stock' => 2]);
        $component->call('pay')->assertRedirect(route('cart'));

        $this->assertSame(0, Order::count());
        Http::assertNothingSent();
        $this->get('/cart')
            ->assertSee('موجودی یا قیمت بعضی از محصولات سبد شما تغییر کرده است')
            ->assertSee('کاهش یافت');
    }

    public function test_gateway_error_keeps_order_and_shows_message(): void
    {
        $this->enableGateway();
        Http::fake(fn () => throw new ConnectionException('timeout'));
        $this->actingAs($this->user);
        Address::factory()->for($this->user)->create();
        $this->fillCart();

        Livewire::test(CheckoutPage::class)->call('pay')->assertRedirect(route('payment.result', Order::first() ?? 'x'));

        $order = Order::sole();
        $this->assertSame(OrderStatus::PaymentFailed, $order->status);
        $this->get(route('payment.result', $order))->assertSee('اتصال به درگاه پرداخت ممکن نشد');
    }
}
