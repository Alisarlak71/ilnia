<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        TrustProxies::flushState();
        URL::forceScheme(null);

        parent::tearDown();
    }

    public function test_security_headers_on_web_pages(): void
    {
        $this->get('/')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_only_over_https(): void
    {
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_https_app_url_forces_https_links_in_production(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://ilnia.ir']);
        (new AppServiceProvider($this->app))->boot();

        $this->assertStringStartsWith('https://', route('products.index'));
    }

    public function test_local_development_follows_the_tunnel_host(): void
    {
        $this->app['env'] = 'local';
        (new AppServiceProvider($this->app))->boot();

        // Through a dev tunnel: links, CSS and fonts on the tunnel's https address.
        $this->get('/', ['X-Forwarded-Host' => 'abc-8000.devtunnels.ms', 'X-Forwarded-Proto' => 'https', 'X-Forwarded-Port' => '443'])
            ->assertSee('https://abc-8000.devtunnels.ms/fonts/vazirmatn-variable.woff2', false);

        // Straight to localhost: everything stays on localhost.
        $this->get('http://localhost:8000/')
            ->assertSee('http://localhost:8000/fonts/vazirmatn-variable.woff2', false)
            ->assertDontSee('devtunnels', false);
    }

    public function test_trusted_proxy_gives_real_visitor_ip(): void
    {
        Route::get('/__ip', fn (\Illuminate\Http\Request $request) => $request->ip())->middleware('web');

        // No proxy configured: the forwarded header is ignored (cannot be spoofed).
        $this->get('/__ip', ['X-Forwarded-For' => '5.6.7.8'])->assertSee('127.0.0.1');

        config(['app.trusted_proxies' => '*']);
        (new AppServiceProvider($this->app))->boot();

        $this->get('/__ip', ['X-Forwarded-For' => '5.6.7.8'])->assertSee('5.6.7.8');
    }

    public function test_payment_callback_is_rate_limited(): void
    {
        Http::fake();

        foreach (range(1, 30) as $i) {
            $this->get('/payment/callback?Authority=S-UNKNOWN&Status=OK')->assertOk();
        }

        $this->get('/payment/callback?Authority=S-UNKNOWN&Status=OK')->assertStatus(429)->assertSee('کمی صبر کنید');
    }

    public function test_admin_flag_cannot_be_mass_assigned(): void
    {
        $user = User::create(['mobile' => '09121234567', 'is_admin' => true]);

        $this->assertFalse($user->fresh()->is_admin);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_security_check_fails_in_development(): void
    {
        $this->artisan('ilnia:security-check')
            ->expectsOutputToContain('APP_DEBUG باید false باشد')
            ->assertFailed();
    }

    public function test_security_check_passes_with_production_settings(): void
    {
        $this->app['env'] = 'production';
        config([
            'app.debug' => false,
            'app.url' => 'https://ilnia.ir',
            'session.secure' => true,
            'services.sms.driver' => 'kavenegar',
            'services.kavenegar.api_key' => 'test-key',
            'services.zarinpal.merchant_id' => '00000000-0000-0000-0000-000000000000',
            'services.zarinpal.sandbox' => false,
        ]);
        User::factory()->admin()->create();

        $this->artisan('ilnia:security-check')
            ->expectsOutputToContain('همهٔ موارد ضروری درست است.')
            ->doesntExpectOutputToContain('test-key')
            ->assertSuccessful();
    }

    public function test_security_check_flags_sandbox_with_real_merchant(): void
    {
        $this->app['env'] = 'production';
        config([
            'app.debug' => false, 'app.url' => 'https://ilnia.ir', 'session.secure' => true,
            'services.sms.driver' => 'kavenegar', 'services.kavenegar.api_key' => 'k',
            'services.zarinpal.merchant_id' => '00000000-0000-0000-0000-000000000000',
            'services.zarinpal.sandbox' => true,
        ]);
        User::factory()->admin()->create();

        $this->artisan('ilnia:security-check')->expectsOutputToContain('ZARINPAL_SANDBOX باید false باشد')->assertFailed();
    }
}
