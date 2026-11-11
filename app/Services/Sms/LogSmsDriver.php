<?php

namespace App\Services\Sms;

use App\Exceptions\SmsException;
use App\Support\Mobile;
use Illuminate\Support\Facades\Log;

/**
 * Development driver: nothing is sent. The message is written to storage/logs/laravel.log
 * so you can read the login code while developing.
 */
class LogSmsDriver implements SmsService
{
    public function sendOtp(string $mobile, string $code): void
    {
        $this->write($mobile, (string) config('services.kavenegar.otp_template'), $code);
    }

    public function sendOrderConfirmation(string $mobile, string $orderNumber): void
    {
        $this->write($mobile, (string) config('services.kavenegar.order_template'), $orderNumber);
    }

    private function write(string $mobile, string $template, string $token): void
    {
        $receptor = Mobile::normalize($mobile) ?? throw new SmsException('Invalid mobile number.');

        if (app()->isProduction()) {
            Log::warning('SMS_DRIVER=log in production: no SMS was sent.', ['template' => $template]);

            return;
        }

        Log::info('SMS (log driver, not sent)', [
            'to' => $receptor,
            'template' => $template,
            'token' => $token,
        ]);
    }
}
