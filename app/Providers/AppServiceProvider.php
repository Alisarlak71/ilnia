<?php

namespace App\Providers;

use App\Services\Sms\KavenegarSmsDriver;
use App\Services\Sms\LogSmsDriver;
use App\Services\Sms\ScreenSmsDriver;
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
        // SMS driver from SMS_DRIVER in .env: "log" (development), "kavenegar" (production) or
        // "screen" (temporary, no SMS panel yet: the login code is shown on screen).
        $this->app->bind(SmsService::class, fn () => match (config('services.sms.driver')) {
            'log' => new LogSmsDriver,
            'kavenegar' => new KavenegarSmsDriver,
            'screen' => new ScreenSmsDriver, // temporary: code shown on the login page
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
        if (app()->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Behind a CDN / reverse proxy (e.g. ArvanCloud, Cloudflare), the visitor's real IP is in a
        // header set by the proxy. Without this, every visitor would share the proxy's IP and the
        // OTP limits per IP would block everyone. Set TRUSTED_PROXIES only when there is a proxy.
        //
        // Local development: always trust the forwarding headers, so a tunnel (VS Code dev tunnel,
        // ngrok…) gets links, CSS and fonts on the tunnel's https address while http://localhost:8000
        // keeps its own. (Pinning every link to APP_URL broke fonts on one of the two: a font from
        // another origin is blocked by the browser without CORS.)
        $proxies = app()->isLocal() ? '*' : config('app.trusted_proxies');
        if (filled($proxies)) {
            TrustProxies::at($proxies);
        }
    }
}
