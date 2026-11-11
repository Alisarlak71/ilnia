<?php

namespace App\Providers;

use App\Services\Sms\KavenegarSmsDriver;
use App\Services\Sms\LogSmsDriver;
use App\Services\Sms\SmsService;
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
        //
    }
}
