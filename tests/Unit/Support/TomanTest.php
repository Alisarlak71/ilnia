<?php

namespace Tests\Unit\Support;

use App\Support\Toman;
use Tests\TestCase;

class TomanTest extends TestCase
{
    public function test_format_uses_persian_digits_separator_and_unit(): void
    {
        $this->assertSame('۴۵۰٬۰۰۰ تومان', Toman::format(450000));
        $this->assertSame('۶۰٬۰۰۰ تومان', Toman::format(60000));
        $this->assertSame('۱٬۲۵۰٬۰۰۰ تومان', Toman::format(1250000));
        $this->assertSame('۰ تومان', Toman::format(0));
        $this->assertSame('۹۹۹ تومان', Toman::format(999));
    }

    public function test_number_without_unit(): void
    {
        $this->assertSame('۴۵۰٬۰۰۰', Toman::number(450000));
        $this->assertSame('−۵٬۰۰۰', Toman::number(-5000));
    }

    public function test_parse_user_input(): void
    {
        $this->assertSame(450000, Toman::parse('۴۵۰٬۰۰۰'));
        $this->assertSame(450000, Toman::parse('450,000'));
        $this->assertSame(450000, Toman::parse(' ۴۵۰۰۰۰ تومان '));
        $this->assertSame(60000, Toman::parse('٦٠٠٠٠'));
        $this->assertSame(1200, Toman::parse(1200));
    }

    public function test_parse_rejects_invalid_input(): void
    {
        $this->assertNull(Toman::parse(''));
        $this->assertNull(Toman::parse('تومان'));
        $this->assertNull(Toman::parse(null));
        $this->assertNull(Toman::parse('1500.5'));
        $this->assertNull(Toman::parse('۱۵۰۰٫۵'));
        $this->assertNull(Toman::parse('9999999999999999'));
    }
}
