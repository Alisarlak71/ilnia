<?php

namespace App\Services;

use App\Exceptions\OtpException;
use App\Exceptions\SmsException;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\Sms\SmsService;
use App\Support\Digits;
use App\Support\Mobile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Login with mobile number + one-time SMS code.
 *
 *   $mobile = $otp->send('0912…', $request->ip());        // sends the code
 *   $user   = $otp->verify($mobile, '12345', $request->ip()); // returns (or creates) the user
 *
 * Security:
 * - only a hash of the code is stored; a new code replaces older ones
 * - the code expires after CODE_TTL seconds and allows MAX_ATTEMPTS wrong tries
 * - a new code at most every RESEND_AFTER seconds per mobile
 * - hourly send limits per mobile and per IP; verify limit per IP
 */
class OtpService
{
    public const CODE_LENGTH = 5;

    public const CODE_TTL = 120;            // seconds

    public const RESEND_AFTER = 60;         // seconds

    public const MAX_ATTEMPTS = 5;          // wrong tries per code

    public const MAX_SENDS_PER_MOBILE = 5;  // per hour

    public const MAX_SENDS_PER_IP = 20;     // per hour

    public const MAX_VERIFIES_PER_IP = 30;  // per 10 minutes

    public function __construct(private readonly SmsService $sms)
    {
    }

    /**
     * Send a new login code. Returns the normalized mobile (09xxxxxxxxx).
     *
     * @throws OtpException
     */
    public function send(string $mobileInput, string $ip): string
    {
        $mobile = Mobile::normalize($mobileInput) ?? throw new OtpException(__('otp.invalid_mobile'));

        $wait = $this->secondsUntilResend($mobile);
        if ($wait > 0) {
            throw new OtpException(__('otp.wait', ['seconds' => Digits::toPersian($wait)]));
        }

        $mobileKey = 'otp-send:mobile:'.$mobile;
        $ipKey = 'otp-send:ip:'.$ip;

        if (RateLimiter::tooManyAttempts($mobileKey, self::MAX_SENDS_PER_MOBILE)
            || RateLimiter::tooManyAttempts($ipKey, self::MAX_SENDS_PER_IP)) {
            throw new OtpException(__('otp.too_many_sends'));
        }

        RateLimiter::hit($mobileKey, 3600);
        RateLimiter::hit($ipKey, 3600);

        $code = $this->generateCode();

        OtpCode::where('mobile', $mobile)->delete();
        $otp = OtpCode::create([
            'mobile' => $mobile,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addSeconds(self::CODE_TTL),
        ]);

        try {
            $this->sms->sendOtp($mobile, $code);
        } catch (SmsException) {
            $otp->delete();

            throw new OtpException(__('otp.sms_failed'));
        }

        return $mobile;
    }

    /**
     * Check the code. On success the code is used up and the user is returned
     * (created on first login).
     *
     * @throws OtpException
     */
    public function verify(string $mobileInput, string $codeInput, string $ip): User
    {
        $mobile = Mobile::normalize($mobileInput) ?? throw new OtpException(__('otp.invalid_mobile'));

        $ipKey = 'otp-verify:ip:'.$ip;
        if (RateLimiter::tooManyAttempts($ipKey, self::MAX_VERIFIES_PER_IP)) {
            throw new OtpException(__('otp.too_many_verifies'));
        }
        RateLimiter::hit($ipKey, 600);

        $code = Digits::toEnglish(trim($codeInput));
        if (! preg_match('/^\d{'.self::CODE_LENGTH.'}$/', $code)) {
            throw new OtpException(__('otp.invalid_code', ['length' => Digits::toPersian(self::CODE_LENGTH)]));
        }

        $otp = OtpCode::where('mobile', $mobile)->latest('id')->first();

        if (! $otp || $otp->isExpired()) {
            throw new OtpException(__('otp.expired'));
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            throw new OtpException(__('otp.too_many_attempts'));
        }

        // Count the try before checking, so parallel requests cannot get extra tries.
        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code_hash)) {
            $remaining = self::MAX_ATTEMPTS - $otp->attempts;

            throw new OtpException($remaining > 0
                ? __('otp.wrong_code', ['remaining' => Digits::toPersian($remaining)])
                : __('otp.too_many_attempts'));
        }

        OtpCode::where('mobile', $mobile)->delete();
        RateLimiter::clear($ipKey);

        return User::firstOrCreate(['mobile' => $mobile]);
    }

    /** Seconds until a new code may be requested for this mobile (0 = now). */
    public function secondsUntilResend(string $mobile): int
    {
        $last = OtpCode::where('mobile', $mobile)->latest('id')->first();

        if (! $last) {
            return 0;
        }

        return max(0, self::RESEND_AFTER - (int) $last->created_at->diffInSeconds(now()));
    }

    private function generateCode(): string
    {
        return str_pad((string) random_int(0, 10 ** self::CODE_LENGTH - 1), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }
}
