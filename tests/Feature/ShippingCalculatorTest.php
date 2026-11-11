<?php

namespace Tests\Feature;

use App\Models\ShippingMethod;
use App\Services\ShippingCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShippingCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private ShippingCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = app(ShippingCalculator::class);
    }

    private function postMethod(?int $threshold = 4000, int $cost = 60000): ShippingMethod
    {
        return ShippingMethod::factory()->create(['cost' => $cost, 'free_over_weight_grams' => $threshold]);
    }

    public static function weights(): array
    {
        // weight => [expected cost, expected remaining grams]
        return [
            'one 1 kg jar (below)' => [1000, 60000, 3000],
            'just below threshold' => [3999, 60000, 1],
            'exactly at threshold (>= is free)' => [4000, 0, 0],
            'above threshold' => [5000, 0, 0],
            'empty' => [0, 60000, 4000],
        ];
    }

    #[DataProvider('weights')]
    public function test_cost_and_remaining_around_threshold(int $weight, int $expectedCost, int $expectedRemaining): void
    {
        $post = $this->postMethod();

        $this->assertSame($expectedCost, $this->calculator->cost($post, $weight));
        $this->assertSame($expectedCost === 0, $this->calculator->isFree($post, $weight));
        $this->assertSame($expectedRemaining, $this->calculator->remainingForFree($post, $weight));
    }

    public function test_four_one_kilo_jars_ship_free(): void
    {
        $this->assertSame(0, $this->calculator->cost($this->postMethod(), 4 * 1000));
    }

    public function test_empty_threshold_means_never_free(): void
    {
        $post = $this->postMethod(threshold: null);

        $this->assertSame(60000, $this->calculator->cost($post, 100000));
        $this->assertFalse($this->calculator->isFree($post, 100000));
        $this->assertNull($this->calculator->remainingForFree($post, 1000));
    }

    public function test_values_come_from_the_database(): void
    {
        $post = $this->postMethod();
        $post->update(['cost' => 75000, 'free_over_weight_grams' => 6000]);

        $this->assertSame(75000, $this->calculator->cost($post->fresh(), 5000));
        $this->assertSame(0, $this->calculator->cost($post->fresh(), 6000));
    }

    public function test_default_method_is_first_active(): void
    {
        $this->assertNull($this->calculator->defaultMethod());

        ShippingMethod::factory()->create(['name' => 'غیرفعال', 'is_active' => false, 'sort' => 0]);
        $second = ShippingMethod::factory()->create(['name' => 'پیک', 'sort' => 2]);
        $first = ShippingMethod::factory()->create(['name' => 'پست', 'sort' => 1]);

        $this->assertTrue($this->calculator->defaultMethod()->is($first));
        $this->assertFalse($this->calculator->defaultMethod()->is($second));
    }
}
