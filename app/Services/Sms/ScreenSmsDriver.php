<?php

namespace App\Services\Sms;

use App\Exceptions\SmsException;
use App\Support\Mobile;
use Illuminate\Support\Facades\Log;

/**
 * TEMPORARY driver while there is no SMS panel (SMS_DRIVER=screen, owner's decision).
 * Nothing is sent: the login code is put in the requester's own session and the login page
 * shows it on screen. Order confirmations are only logged.
 *
 * WARNING: anyone can log in with ANY mobile number in this mode. Never use it with real
 * customers; `php artisan ilnia:security-check` reports it as a failure in production.
 */
class ScreenSmsDriver implements SmsService
{
    public const SESSION_KEY = 'sms_screen_otp';

    public function sendOtp(string $mobile, string $code): void
    {
        $receptor = Mobile::normalize($mobile) ?? throw new SmsException('Invalid mobile number.');

        session()->put(self::SESSION_KEY, ['mobile' => $receptor, 'code' => $code]);
    }

    public function sendOrderConfirmation(string $mobile, string $orderNumber): void
    {
        Log::info('SMS (screen driver, not sent): order confirmation', ['order' => $orderNumber]);
    }

    /** The code waiting to be shown for this mobile (read once), or null. */
    public static function pullCodeFor(string $mobile): ?string
    {
        $pending = session()->pull(self::SESSION_KEY);

        return is_array($pending) && ($pending['mobile'] ?? null) === $mobile ? (string) $pending['code'] : null;
    }
}
