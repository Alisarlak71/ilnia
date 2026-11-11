<?php

namespace App\Services\Sms;

use App\Exceptions\SmsException;

/**
 * Sends the shop's SMS messages through pre-approved templates (Kavenegar "verify lookup").
 * Free-text SMS is intentionally not supported.
 *
 * Implementations: LogSmsDriver (development/tests), KavenegarSmsDriver (production).
 * Chosen with SMS_DRIVER in .env (see AppServiceProvider).
 */
interface SmsService
{
    /**
     * Login code. Template: KAVENEGAR_OTP_TEMPLATE.
     *
     * @throws SmsException
     */
    public function sendOtp(string $mobile, string $code): void;

    /**
     * Order confirmation. Template: KAVENEGAR_ORDER_TEMPLATE.
     *
     * @throws SmsException
     */
    public function sendOrderConfirmation(string $mobile, string $orderNumber): void;
}
