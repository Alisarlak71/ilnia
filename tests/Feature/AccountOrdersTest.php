<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountOrdersTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['mobile' => '09121234567']);
    }

    private function order(array $attributes = [], ?User $user = null): Order
    {
        $order = Order::factory()->for($user ?? $this->user)->create($attributes + [
            'subtotal' => 960000, 'shipping_cost' => 60000, 'total' => 1020000,
            'shipping_method_name' => 'پست', 'province' => 'فارس', 'city' => 'شیراز', 'address_line' => 'خیابان نمونه',
        ]);
        $order->items()->create([
            'product_variant_id' => null, 'product_name' => 'عسل نمونه', 'variant_title' => '۱ کیلوگرم',
            'weight_grams' => 1000, 'unit_price' => 480000, 'quantity' => 2, 'line_total' => 960000,
        ]);

        return $order;
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/account/orders')->assertRedirect(route('login'));
    }

    public function test_empty_state(): void
    {
        $this->actingAs($this->user)->get('/account/orders')
            ->assertOk()
            ->assertSee('<title>سفارش‌های من | ایلنیا</title>', false)
            ->assertSee('هنوز سفارشی ثبت نکرده‌اید');
    }

    public function test_list_shows_only_own_orders_newest_first(): void
    {
        $old = $this->order(['status' => OrderStatus::Delivered]);
        $new = $this->order(['status' => OrderStatus::Shipped]);
        $foreign = $this->order([], User::create(['mobile' => '09351234567']));

        $this->actingAs($this->user)->get('/account/orders')
            ->assertSeeInOrder([$new->number, $old->number])
            ->assertSee('ارسال‌شده')
            ->assertSee('تحویل‌شده')
            ->assertSee('۱٬۰۲۰٬۰۰۰ تومان')
            ->assertSee('۱ قلم')
            ->assertDontSee($foreign->number);
    }

    public function test_list_is_paginated(): void
    {
        foreach (range(1, 12) as $i) {
            $this->order();
        }

        $this->actingAs($this->user)->get('/account/orders')->assertSee('بعدی');
        $this->actingAs($this->user)->get('/account/orders?page=2')->assertOk()->assertSee('قبلی');
    }

    public function test_detail_shows_items_address_and_tracking_code(): void
    {
        $order = $this->order(['status' => OrderStatus::Shipped, 'tracking_code' => '123456789012345678', 'customer_note' => 'عصر تحویل شود']);

        $this->actingAs($this->user)->get(route('account.orders.show', $order->number))
            ->assertOk()
            ->assertSee($order->number)
            ->assertSee('سفارش به پست تحویل داده شد.')
            ->assertSee('123456789012345678')
            ->assertSee('https://tracking.post.ir/')
            ->assertSeeInOrder(['عسل نمونه (۱ کیلوگرم)', '× ۲'])
            ->assertSee('فارس، شیراز، خیابان نمونه')
            ->assertSee('عصر تحویل شود')
            ->assertDontSee('پرداخت دوباره');
    }

    public function test_detail_without_tracking_code_hides_tracking_box(): void
    {
        $order = $this->order(['status' => OrderStatus::Processing]);

        $this->actingAs($this->user)->get(route('account.orders.show', $order->number))
            ->assertSee('در حال آماده‌سازی و بسته‌بندی')
            ->assertDontSee('کد رهگیری پست');
    }

    public function test_someone_elses_order_is_404(): void
    {
        $foreign = $this->order([], User::create(['mobile' => '09351234567']));

        $this->actingAs($this->user)->get(route('account.orders.show', $foreign->number))->assertNotFound();
    }

    public function test_unpaid_order_shows_retry_only_when_gateway_enabled(): void
    {
        $order = $this->order(['status' => OrderStatus::PaymentFailed]);

        $this->actingAs($this->user)->get(route('account.orders.show', $order->number))->assertDontSee('پرداخت دوباره');

        config(['services.zarinpal.merchant_id' => '00000000-0000-0000-0000-000000000000']);
        $this->actingAs($this->user)->get(route('account.orders.show', $order->number))
            ->assertSee('پرداخت دوباره')
            ->assertSee(route('payment.retry', $order));
    }

    public function test_header_links_to_my_orders(): void
    {
        $this->actingAs($this->user)->get('/')->assertSee(route('account.orders'));
    }
}
