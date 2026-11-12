<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_persian_rtl_layout(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="fa" dir="rtl">', false);
        $response->assertSee('<title>ایلنیا | به رسم نیاکان</title>', false);
        $response->assertSee('/favicon.svg', false);
    }

    public function test_header_and_footer_are_rendered(): void
    {
        $response = $this->get('/');

        $response->assertSee('<header', false);
        $response->assertSee('<footer', false);
        $response->assertSee('id="main"', false);
        $response->assertSee('brand/logo-mark-gold.svg', false);
        $response->assertSee('به رسم نیاکان');
        $response->assertSeeInOrder(['اصلش را می‌فروشیم،', 'نه ادعایش را']);
    }

    public function test_page_title_can_be_set(): void
    {
        $html = (string) $this->blade('<x-layouts.app title="سبد خرید">محتوا</x-layouts.app>');

        $this->assertStringContainsString('<title>سبد خرید | ایلنیا</title>', $html);
        $this->assertStringContainsString('محتوا', $html);
    }
}
