<?php

namespace Tests\Feature;

use App\Exceptions\CartException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = app(CartService::class);
    }

    private function variant(array $attributes = []): ProductVariant
    {
        return ProductVariant::factory()->create($attributes + ['price' => 480000, 'stock' => 10, 'weight_grams' => 1000]);
    }

    public function test_starts_empty(): void
    {
        $this->assertTrue($this->cart->isEmpty());
        $this->assertSame(0, $this->cart->count());
        $this->assertSame(0, $this->cart->subtotal());
        $this->assertCount(0, $this->cart->lines());
    }

    public function test_add_and_add_again_sums_quantity(): void
    {
        $variant = $this->variant();

        $this->cart->add($variant->id);
        $this->cart->add($variant->id, 2);

        $this->assertSame([$variant->id => 3], $this->cart->raw());
        $this->assertSame(3, $this->cart->count());
    }

    public function test_session_stores_only_ids_and_quantities(): void
    {
        $variant = $this->variant();
        $this->cart->add($variant->id, 2);

        $this->assertSame([$variant->id => 2], session(CartService::SESSION_KEY));
    }

    public function test_subtotal_and_weight_are_calculated_from_database(): void
    {
        $kilo = $this->variant(['price' => 480000, 'weight_grams' => 1000]);
        $two = $this->variant(['price' => 930000, 'weight_grams' => 2000]);

        $this->cart->add($kilo->id, 2);
        $this->cart->add($two->id, 1);

        $this->assertSame(2 * 480000 + 930000, $this->cart->subtotal());
        $this->assertSame(4000, $this->cart->totalWeightGrams());

        $line = $this->cart->lines()->firstWhere('variant.id', $kilo->id);
        $this->assertSame(480000, $line->unitPrice);
        $this->assertSame(960000, $line->lineTotal);
        $this->assertSame(2000, $line->weightGrams);
    }

    public function test_price_change_in_database_is_reflected_immediately(): void
    {
        $variant = $this->variant(['price' => 480000]);
        $this->cart->add($variant->id, 2);

        $variant->update(['price' => 500000]);

        $this->assertSame(1000000, $this->cart->subtotal());
    }

    public function test_tampered_session_price_data_is_ignored(): void
    {
        $variant = $this->variant(['price' => 480000]);
        // Even if someone manages to put extra data in the session, only id => quantity is used.
        session([CartService::SESSION_KEY => [$variant->id => 1]]);

        $this->assertSame(480000, $this->cart->subtotal());
    }

    public function test_cannot_add_more_than_stock(): void
    {
        $variant = $this->variant(['stock' => 3]);
        $this->cart->add($variant->id, 2);

        try {
            $this->cart->add($variant->id, 2);
            $this->fail('Expected CartException');
        } catch (CartException $e) {
            $this->assertStringContainsString('فقط ۳ عدد موجود است', $e->getMessage());
        }

        $this->assertSame(2, $this->cart->count());
    }

    public function test_cannot_add_more_than_max_per_item(): void
    {
        $variant = $this->variant(['stock' => 500]);

        $this->expectException(CartException::class);
        $this->cart->add($variant->id, CartService::MAX_PER_ITEM + 1);
    }

    public function test_cannot_add_unavailable_variants(): void
    {
        $cases = [
            'out of stock' => $this->variant(['stock' => 0]),
            'no price' => $this->variant(['price' => null]),
            'inactive variant' => $this->variant(['is_active' => false]),
            'hidden product' => $this->variant(['product_id' => Product::factory()->inactive()->create()->id]),
        ];

        foreach ($cases as $name => $variant) {
            try {
                $this->cart->add($variant->id);
                $this->fail("Expected CartException for: {$name}");
            } catch (CartException $e) {
                $this->assertSame('این محصول در حال حاضر قابل خرید نیست.', $e->getMessage());
            }
        }

        $this->assertTrue($this->cart->isEmpty());
    }

    public function test_cannot_add_unknown_variant_or_bad_quantity(): void
    {
        $this->expectException(CartException::class);
        $this->cart->add(99999);
    }

    public function test_zero_or_negative_quantity_is_rejected_on_add(): void
    {
        $variant = $this->variant();

        foreach ([0, -1] as $quantity) {
            try {
                $this->cart->add($variant->id, $quantity);
                $this->fail("Expected CartException for quantity {$quantity}");
            } catch (CartException) {
                // expected
            }
        }

        $this->assertTrue($this->cart->isEmpty());
    }

    public function test_set_quantity_replaces_and_zero_removes(): void
    {
        $variant = $this->variant();
        $this->cart->add($variant->id, 5);

        $this->cart->setQuantity($variant->id, 2);
        $this->assertSame(2, $this->cart->count());

        $this->cart->setQuantity($variant->id, 0);
        $this->assertTrue($this->cart->isEmpty());
    }

    public function test_set_quantity_above_stock_is_rejected(): void
    {
        $variant = $this->variant(['stock' => 4]);
        $this->cart->add($variant->id);

        $this->expectException(CartException::class);
        $this->cart->setQuantity($variant->id, 5);
    }

    public function test_remove_and_clear(): void
    {
        $a = $this->variant();
        $b = $this->variant();
        $this->cart->add($a->id);
        $this->cart->add($b->id);

        $this->cart->remove($a->id);
        $this->assertSame([$b->id => 1], $this->cart->raw());

        $this->cart->clear();
        $this->assertTrue($this->cart->isEmpty());
    }

    public function test_items_that_became_unavailable_are_removed_with_message(): void
    {
        $stays = $this->variant();
        $hidden = $this->variant();
        $deleted = $this->variant();
        foreach ([$stays, $hidden, $deleted] as $variant) {
            $this->cart->add($variant->id);
        }

        $hidden->product->update(['is_active' => false]);
        $deleted->delete();

        $lines = $this->cart->lines();

        $this->assertCount(1, $lines);
        $this->assertSame([$stays->id => 1], $this->cart->raw());

        $messages = $this->cart->pullAdjustments();
        $this->assertCount(2, $messages);
        $this->assertStringContainsString('از سبد شما حذف شد', $messages[0]);
        $this->assertSame('یکی از محصولات سبد شما دیگر موجود نیست و حذف شد.', $messages[1]);

        // Messages are shown only once.
        $this->assertSame([], $this->cart->pullAdjustments());
    }

    public function test_quantity_is_reduced_when_stock_drops(): void
    {
        $variant = $this->variant(['stock' => 10]);
        $this->cart->add($variant->id, 6);

        $variant->update(['stock' => 4]);

        $this->assertSame(4, $this->cart->lines()->first()->quantity);
        $this->assertSame(4, $this->cart->count());
        $this->assertStringContainsString('به ۴ (موجودی فعلی) کاهش یافت', $this->cart->pullAdjustments()[0]);
    }

    public function test_corrupted_session_value_is_treated_as_empty(): void
    {
        session([CartService::SESSION_KEY => 'not-an-array']);

        $this->assertTrue($this->cart->isEmpty());
        $this->assertCount(0, $this->cart->lines());
    }

    public function test_malformed_session_rows_are_ignored(): void
    {
        $variant = $this->variant();
        session([CartService::SESSION_KEY => [$variant->id => 2, 'abc' => 1, 5 => 'x', 6 => -3, 7 => 1.5]]);

        $this->assertSame([$variant->id => 2], $this->cart->raw());
        $this->assertSame(2, $this->cart->count());
        $this->assertSame(960000, $this->cart->subtotal());
    }
}
