<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    private function footer(): string
    {
        $html = $this->get('/')->assertOk()->getContent();

        return substr($html, strpos($html, '<footer'));
    }

    public function test_footer_has_the_quick_menu(): void
    {
        $footer = $this->footer();

        $this->assertStringContainsString('دسترسی سریع', $footer);
        foreach ([route('home'), route('products.index'), route('cart'), route('login')] as $url) {
            $this->assertStringContainsString('href="'.$url.'"', $footer);
        }
    }

    public function test_logged_in_customer_gets_my_orders_instead_of_login(): void
    {
        $this->actingAs(User::create(['mobile' => '09121234567']));
        $footer = $this->footer();

        $this->assertStringContainsString('href="'.route('account.orders').'"', $footer);
        $this->assertStringNotContainsString('ورود به حساب', $footer);
    }

    public function test_social_links_are_hidden_until_configured(): void
    {
        config(['shop.social' => ['instagram' => '', 'telegram' => '', 'bale' => '']]);
        $footer = $this->footer();

        $this->assertStringNotContainsString('ما را دنبال کنید', $footer);
        $this->assertStringNotContainsString('instagram.com', $footer);
        $this->assertStringNotContainsString('t.me', $footer);
        $this->assertStringNotContainsString('ble.ir', $footer);
    }

    public function test_configured_social_links_are_shown(): void
    {
        config(['shop.social' => ['instagram' => 'ilnia_sample', 'telegram' => 'ilnia_sample', 'bale' => 'ilnia_sample']]);
        $footer = $this->footer();

        $this->assertStringContainsString('ما را دنبال کنید', $footer);
        $this->assertStringContainsString('href="https://instagram.com/ilnia_sample"', $footer);
        $this->assertStringContainsString('href="https://t.me/ilnia_sample"', $footer);
        $this->assertStringContainsString('href="https://ble.ir/ilnia_sample"', $footer);
        $this->assertStringContainsString('rel="noopener"', $footer);
        $this->assertStringContainsString('@ilnia_sample', $footer);
    }

    public function test_only_the_configured_networks_are_shown(): void
    {
        config(['shop.social' => ['instagram' => 'ilnia_sample', 'telegram' => '', 'bale' => '']]);
        $footer = $this->footer();

        $this->assertStringContainsString('instagram.com/ilnia_sample', $footer);
        $this->assertStringNotContainsString('t.me', $footer);
        $this->assertStringNotContainsString('ble.ir', $footer);
    }
}
