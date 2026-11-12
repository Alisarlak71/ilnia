<?php

namespace App\Providers;

use App\Services\Sms\KavenegarSmsDriver;
use App\Services\Sms\LogSmsDriver;
use App\Services\Sms\SmsService;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // SMS driver from SMS_DRIVER in .env: "log" (development) or "kavenegar" (production).
        $this->app->bind(SmsService::class, fn () => match (config('services.sms.driver')) {
            'log' => new LogSmsDriver,
            'kavenegar' => new KavenegarSmsDriver,
            default => throw new InvalidArgumentException('Unknown SMS_DRIVER: '.config('services.sms.driver')),
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Real site on https://… : every generated link and redirect uses HTTPS, even when the
        // host's proxy talks to PHP over plain HTTP.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Behind a CDN / reverse proxy (e.g. ArvanCloud, Cloudflare), the visitor's real IP is in a
        // header set by the proxy. Without this, every visitor would share the proxy's IP and the
        // OTP limits per IP would block everyone. Set TRUSTED_PROXIES only when there is a proxy.
        if (filled(config('app.trusted_proxies'))) {
            TrustProxies::at(config('app.trusted_proxies'));
        }
    }
}
