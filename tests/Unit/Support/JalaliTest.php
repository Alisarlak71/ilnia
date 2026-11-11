<?php

namespace Tests\Unit\Support;

use App\Support\Jalali;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class JalaliTest extends TestCase
{
    public function test_date_formats(): void
    {
        $date = CarbonImmutable::parse('2026-11-11 22:30:00', 'Asia/Tehran');

        $this->assertSame('۱۴۰۵/۰۸/۲۰', Jalali::date($date));
        $this->assertSame('۲۰ آبان ۱۴۰۵', Jalali::long($date));
        $this->assertSame('۱۴۰۵/۰۸/۲۰ ۲۲:۳۰', Jalali::dateTime($date));
        $this->assertSame('چهارشنبه ۲۰ آبان ۱۴۰۵', Jalali::format($date, 'EEEE d MMMM yyyy'));
    }

    public function test_nowruz_is_first_of_farvardin(): void
    {
        $this->assertSame('۱۴۰۵/۰۱/۰۱', Jalali::date(CarbonImmutable::parse('2026-03-21', 'Asia/Tehran')));
        $this->assertSame('۱۴۰۴/۱۲/۲۹', Jalali::date(CarbonImmutable::parse('2026-03-20', 'Asia/Tehran')));
    }

    public function test_uses_tehran_time_for_utc_dates(): void
    {
        // 21:00 UTC on 2026-11-11 is already 00:30 on 2026-11-12 in Tehran (21 Aban).
        $utc = CarbonImmutable::parse('2026-11-11 21:00:00', 'UTC');

        $this->assertSame('۱۴۰۵/۰۸/۲۱ ۰۰:۳۰', Jalali::dateTime($utc));
    }

    public function test_null_returns_empty_string(): void
    {
        $this->assertSame('', Jalali::date(null));
    }
}
