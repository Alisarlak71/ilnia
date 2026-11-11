<?php

namespace Tests\Support;

use App\Exceptions\SmsException;
use App\Services\Sms\SmsService;

/**
 * Test double: records messages instead of sending them, so tests can read the OTP code.
 */
class FakeSmsService implements SmsService
{
    /** @var array<int, array{type: string, mobile: string, token: string}> */
    public array $sent = [];

    public bool $fail = false;

    public function sendOtp(string $mobile, string $code): void
    {
        $this->record('otp', $mobile, $code);
    }

    public function sendOrderConfirmation(string $mobile, string $orderNumber): void
    {
        $this->record('order', $mobile, $orderNumber);
    }

    public function lastCode(): ?string
    {
        $otp = array_values(array_filter($this->sent, fn ($m) => $m['type'] === 'otp'));

        return $otp === [] ? null : end($otp)['token'];
    }

    private function record(string $type, string $mobile, string $token): void
    {
        if ($this->fail) {
            throw new SmsException('Fake failure');
        }

        $this->sent[] = ['type' => $type, 'mobile' => $mobile, 'token' => $token];
    }
}
