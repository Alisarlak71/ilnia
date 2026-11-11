<?php

namespace Tests\Feature;

use Tests\TestCase;

class LogoComponentTest extends TestCase
{
    public function test_dark_variant_uses_gold_mark_and_cream_text(): void
    {
        $html = (string) $this->blade('<x-logo />');

        $this->assertStringContainsString('brand/logo-mark-gold.svg', $html);
        $this->assertStringContainsString('text-cream', $html);
        $this->assertStringContainsString('ایلنیا', $html);
        $this->assertStringContainsString('href="'.route('home').'"', $html);
        $this->assertStringContainsString('aria-label="ایلنیا، صفحهٔ اصلی"', $html);
    }

    public function test_light_variant_uses_ink_mark_and_ink_text(): void
    {
        $html = (string) $this->blade('<x-logo variant="light" />');

        $this->assertStringContainsString('brand/logo-mark-ink.svg', $html);
        $this->assertStringContainsString('text-ink', $html);
        $this->assertStringNotContainsString('logo-mark-gold.svg', $html);
    }

    public function test_size_and_extra_classes_are_applied(): void
    {
        $html = (string) $this->blade('<x-logo :size="32" class="mt-4" />');

        $this->assertStringContainsString('width="32"', $html);
        $this->assertStringContainsString('height="32"', $html);
        $this->assertStringContainsString('inline-flex items-center gap-3 mt-4', $html);
    }
}
