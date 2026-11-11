<?php

namespace Tests\Unit\Support;

use App\Support\Digits;
use PHPUnit\Framework\TestCase;

class DigitsTest extends TestCase
{
    public function test_persian_and_arabic_digits_become_english(): void
    {
        $this->assertSame('0123456789', Digits::toEnglish('۰۱۲۳۴۵۶۷۸۹'));
        $this->assertSame('0123456789', Digits::toEnglish('٠١٢٣٤٥٦٧٨٩'));
        $this->assertSame('کد 1234', Digits::toEnglish('کد ۱۲۳۴'));
        $this->assertSame('', Digits::toEnglish(null));
    }

    public function test_english_and_arabic_digits_become_persian(): void
    {
        $this->assertSame('۱۴۰۵', Digits::toPersian(1405));
        $this->assertSame('۰۹۱۲', Digits::toPersian('0912'));
        $this->assertSame('۴۵', Digits::toPersian('٤٥'));
        $this->assertSame('', Digits::toPersian(null));
    }

    public function test_round_trip(): void
    {
        $this->assertSame('2026-11-11', Digits::toEnglish(Digits::toPersian('2026-11-11')));
    }
}
