<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Orders\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\ShippingMethods\Pages\CreateShippingMethod;
use App\Filament\Resources\ShippingMethods\Pages\EditShippingMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Database\Seeders\ShippingMethodSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrdersAdminTest extends TestCase
{
    use RefreshDatabase;

    // --- models -------------------------------------------------------------

    public function test_order_number_is_generated_and_unique(): void
    {
        $a = Order::factory()->create();
        $b = Order::factory()->create();

        $this->assertMatchesRegularExpression('/^IL-\d{6}-\d{4}$/', $a->number);
        $this->assertNotSame($a->number, $b->number);
    }

    public function test_relations_and_casts(): void
    {
        $order = Order::factory()->status(OrderStatus::Paid)->create();
        OrderItem::factory()->for($order)->create();
        Payment::factory()->for($order)->create(['status' => PaymentStatus::Paid, 'payload' => ['code' => 100]]);

        $order = $order->fresh();
        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertCount(1, $order->items);
        $this->assertSame(PaymentStatus::Paid, $order->payments->first()->status);
        $this->assertSame(['code' => 100], $order->payments->first()->payload);
        $this->assertTrue($order->user->orders->contains($order));
        $this->assertNotNull($order->paid_at);
    }

    public function test_new_order_defaults_to_pending_payment(): void
    {
        $this->assertSame(OrderStatus::PendingPayment, (new Order)->status);
        $this->assertSame(0, (new Order)->discount);
    }

    public function test_order_with_payments_cannot_be_deleted(): void
    {
        $order = Order::factory()->create();
        Payment::factory()->for($order)->create();

        $this->expectException(QueryException::class);
        $order->delete();
    }

    public function test_order_survives_customer_and_variant_deletion(): void
    {
        $variant = ProductVariant::factory()->create();
        $order = Order::factory()->create();
        $item = OrderItem::factory()->for($order)->create(['product_variant_id' => $variant->id, 'product_name' => 'عسل نمونه']);

        $variant->delete();
        $order->user->delete();

        $this->assertNull($order->fresh()->user_id);
        $this->assertNull($item->fresh()->product_variant_id);
        $this->assertSame('عسل نمونه', $item->fresh()->product_name);
    }

    public function test_status_labels_and_transitions(): void
    {
        $this->assertSame('در انتظار پرداخت', OrderStatus::PendingPayment->getLabel());
        $this->assertSame([OrderStatus::Cancelled], OrderStatus::PendingPayment->adminTransitions());
        $this->assertNotContains(OrderStatus::Paid, OrderStatus::PendingPayment->adminTransitions());
        $this->assertSame([], OrderStatus::Delivered->adminTransitions());
        $this->assertTrue(OrderStatus::Shipped->isPaid());
        $this->assertFalse(OrderStatus::PaymentFailed->isPaid());
    }

    // --- shipping seeder ------------------------------------------------------

    public function test_seeder_creates_post_with_owner_values(): void
    {
        $this->seed(ShippingMethodSeeder::class);

        $post = ShippingMethod::sole();
        $this->assertSame('پست', $post->name);
        $this->assertSame(60000, $post->cost);
        $this->assertSame(4000, $post->free_over_weight_grams);
        $this->assertTrue($post->is_active);
    }

    public function test_seeder_does_not_overwrite_admin_changes(): void
    {
        $this->seed(ShippingMethodSeeder::class);
        ShippingMethod::sole()->update(['cost' => 75000]);

        $this->seed(ShippingMethodSeeder::class);

        $this->assertSame(75000, ShippingMethod::sole()->cost);
        $this->assertSame(1, ShippingMethod::count());
    }

    // --- admin panel ------------------------------------------------------------

    private function actingAsAdmin(): void
    {
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_admin_pages_load_and_create_order_is_not_available(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->create();

        $this->get('/admin/orders')->assertOk();
        $this->get("/admin/orders/{$order->id}/edit")->assertOk()->assertSee($order->number);
        $this->get('/admin/orders/create')->assertNotFound();
        $this->get('/admin/shipping-methods')->assertOk();
    }

    public function test_non_admin_cannot_see_orders(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/orders')->assertForbidden();
    }

    public function test_orders_list_shows_records(): void
    {
        $this->actingAsAdmin();
        $orders = Order::factory()->count(3)->create();

        Livewire::test(ListOrders::class)->assertCanSeeTableRecords($orders);
    }

    public function test_admin_can_ship_a_paid_order_with_tracking_code(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->status(OrderStatus::Paid)->create();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm(['status' => OrderStatus::Shipped->value, 'tracking_code' => ' 123456789012345678 '])
            ->call('save')
            ->assertHasNoFormErrors();

        $order->refresh();
        $this->assertSame(OrderStatus::Shipped, $order->status);
        $this->assertSame('123456789012345678', $order->tracking_code);
    }

    public function test_shipping_requires_a_tracking_code(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->status(OrderStatus::Paid)->create();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm(['status' => OrderStatus::Shipped->value, 'tracking_code' => '   '])
            ->call('save')
            ->assertHasFormErrors(['tracking_code']);

        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
    }

    public function test_tracking_code_with_persian_digits_is_normalized(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->status(OrderStatus::Paid)->create();

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm(['status' => OrderStatus::Shipped->value, 'tracking_code' => '۱۲۳۴ ۵۶۷۸ ۹۰'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('1234567890', $order->fresh()->tracking_code);
    }

    public function test_admin_cannot_mark_unpaid_order_as_paid(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->create(); // pending_payment

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->fillForm(['status' => OrderStatus::Paid->value])
            ->call('save')
            ->assertHasFormErrors(['status']);

        $this->assertSame(OrderStatus::PendingPayment, $order->fresh()->status);
    }

    public function test_save_cannot_change_amounts_or_snapshot(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->status(OrderStatus::Paid)->create(['total' => 540000, 'recipient_name' => 'گیرندهٔ اصلی']);

        Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
            ->set('data.total', 1)
            ->set('data.recipient_name', 'تغییر')
            ->call('save');

        $order->refresh();
        $this->assertSame(540000, $order->total);
        $this->assertSame('گیرندهٔ اصلی', $order->recipient_name);
    }

    public function test_status_options_are_current_plus_allowed(): void
    {
        $order = Order::factory()->status(OrderStatus::Shipped)->create();

        $this->assertSame(
            ['shipped' => 'ارسال‌شده', 'delivered' => 'تحویل‌شده', 'needs_attention' => 'نیاز به بررسی'],
            \App\Filament\Resources\Orders\Schemas\OrderForm::statusOptions($order),
        );
    }

    public function test_items_and_payments_relation_managers_are_read_only(): void
    {
        $this->actingAsAdmin();
        $order = Order::factory()->create();
        $items = OrderItem::factory()->count(2)->for($order)->create();
        $payment = Payment::factory()->for($order)->create();

        Livewire::test(ItemsRelationManager::class, ['ownerRecord' => $order, 'pageClass' => EditOrder::class])
            ->assertCanSeeTableRecords($items)
            ->assertTableActionDoesNotExist('create')
            ->assertTableActionDoesNotExist('delete');

        Livewire::test(PaymentsRelationManager::class, ['ownerRecord' => $order, 'pageClass' => EditOrder::class])
            ->assertCanSeeTableRecords([$payment])
            ->assertTableActionDoesNotExist('create');
    }

    public function test_navigation_badge_counts_orders_needing_attention(): void
    {
        Order::factory()->count(2)->status(OrderStatus::NeedsAttention)->create();
        Order::factory()->create();

        $this->assertSame('۲', \App\Filament\Resources\Orders\OrderResource::getNavigationBadge());
    }

    public function test_admin_edits_shipping_cost_and_threshold_with_persian_digits(): void
    {
        $this->actingAsAdmin();
        $this->seed(ShippingMethodSeeder::class);
        $post = ShippingMethod::sole();

        Livewire::test(EditShippingMethod::class, ['record' => $post->getRouteKey()])
            ->fillForm(['cost' => '۷۰٬۰۰۰', 'free_over_weight_grams' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();
        $this->assertSame(70000, $post->cost);
        $this->assertNull($post->free_over_weight_grams);
    }

    public function test_admin_creates_shipping_method(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateShippingMethod::class)
            ->fillForm(['name' => 'پیک', 'cost' => '50000', 'free_over_weight_grams' => '3000', 'sort' => '1', 'is_active' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('shipping_methods', ['name' => 'پیک', 'cost' => 50000, 'free_over_weight_grams' => 3000]);
    }
}
