<?php

namespace Tests\Unit\Support;

use App\Support\Weight;
use PHPUnit\Framework\TestCase;

class WeightTest extends TestCase
{
    public function test_grams_to_persian_kilograms(): void
    {
        $this->assertSame('۳', Weight::kg(3000));
        $this->assertSame('۱٫۵', Weight::kg(1500));
        $this->assertSame('۰٫۲۵', Weight::kg(250));
        $this->assertSame('۰٫۰۱', Weight::kg(5));      // rounded to 2 decimals
        $this->assertSame('۰', Weight::kg(0));
        $this->assertSame('۱۲', Weight::kg(12000));
    }
}
