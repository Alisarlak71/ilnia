<?php

namespace Tests\Feature;

use App\Support\SupportLinks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_links_are_built_from_config(): void
    {
        config(['shop.support' => ['whatsapp' => '+98 912 345 6789', 'telegram' => 'ilnia_support', 'bale' => 'ilnia']]);

        $this->assertSame([
            ['key' => 'whatsapp', 'url' => 'https://wa.me/989123456789'],
            ['key' => 'telegram', 'url' => 'https://t.me/ilnia_support'],
            ['key' => 'bale', 'url' => 'https://ble.ir/ilnia'],
        ], SupportLinks::all());
    }

    public function test_persian_digits_in_whatsapp_number_are_accepted(): void
    {
        config(['shop.support' => ['whatsapp' => '۰۹۱۲۳۴۵۶۷۸۹', 'telegram' => '', 'bale' => '']]);
        $this->app['env'] = 'production';

        $this->assertSame([['key' => 'whatsapp', 'url' => 'https://wa.me/989123456789']], SupportLinks::all());
    }

    public function test_production_hides_empty_and_invalid_accounts(): void
    {
        config(['shop.support' => ['whatsapp' => '12345', 'telegram' => '', 'bale' => 'ilnia']]);
        $this->app['env'] = 'production';

        $this->assertSame([['key' => 'bale', 'url' => 'https://ble.ir/ilnia']], SupportLinks::all());
    }

    public function test_outside_production_empty_accounts_show_as_placeholders(): void
    {
        config(['shop.support' => ['whatsapp' => '', 'telegram' => '', 'bale' => '']]);

        $this->assertSame(['#', '#', '#'], array_column(SupportLinks::all(), 'url'));
    }

    public function test_demo_flag_shows_placeholders_in_production(): void
    {
        config(['shop.support' => ['whatsapp' => '', 'telegram' => 'ilnia', 'bale' => ''], 'shop.placeholder_links' => true]);
        $this->app['env'] = 'production';

        $this->assertSame(['#', 'https://t.me/ilnia', '#'], array_column(SupportLinks::all(), 'url'));
        $this->assertStringContainsString('support-links', (string) $this->blade('<x-site.support-button />'));
    }

    public function test_button_is_rendered_with_links(): void
    {
        config(['shop.support' => ['whatsapp' => '09123456789', 'telegram' => 'ilnia_support', 'bale' => '']]);

        $response = $this->get('/');

        $response->assertSee('aria-controls="support-links"', false);
        $response->assertSee('https://wa.me/989123456789', false);
        $response->assertSee('https://t.me/ilnia_support', false);
        $response->assertSee('واتس‌اپ');
    }

    public function test_button_is_hidden_in_production_without_accounts(): void
    {
        config(['shop.support' => ['whatsapp' => '', 'telegram' => '', 'bale' => '']]);
        $this->app['env'] = 'production';

        $html = (string) $this->blade('<x-site.support-button />');

        $this->assertStringNotContainsString('support-links', $html);
    }
}
