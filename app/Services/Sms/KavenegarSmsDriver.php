<?php

namespace App\Services\Sms;

use App\Exceptions\SmsException;
use App\Support\Mobile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Production driver: Kavenegar "verify lookup" API (template-based SMS).
 *
 *   POST https://api.kavenegar.com/v1/{API_KEY}/verify/lookup.json
 *   receptor=09xxxxxxxxx & template=otp & token=12345
 *
 * The template's text is defined and approved in the Kavenegar panel. It must use the
 * variable %token% (ASSUMPTION until confirmed — see docs/ARCHITECTURE.md).
 * `token` may not contain spaces.
 *
 * The API key is part of the URL, so the URL is never logged.
 */
class KavenegarSmsDriver implements SmsService
{
    public function sendOtp(string $mobile, string $code): void
    {
        $this->lookup($mobile, (string) config('services.kavenegar.otp_template'), $code);
    }

    public function sendOrderConfirmation(string $mobile, string $orderNumber): void
    {
        $this->lookup($mobile, (string) config('services.kavenegar.order_template'), $orderNumber);
    }

    private function lookup(string $mobile, string $template, string $token): void
    {
        $apiKey = (string) config('services.kavenegar.api_key');

        if ($apiKey === '') {
            throw new SmsException('KAVENEGAR_API_KEY is not set.');
        }

        $receptor = Mobile::normalize($mobile) ?? throw new SmsException('Invalid mobile number.');

        if ($template === '' || $token === '' || preg_match('/\s/u', $token)) {
            throw new SmsException('Template is empty or token is empty / contains spaces.');
        }

        $url = rtrim((string) config('services.kavenegar.base_url'), '/')."/{$apiKey}/verify/lookup.json";

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout((int) config('services.kavenegar.timeout', 10))
                ->post($url, [
                    'receptor' => $receptor,
                    'template' => $template,
                    'token' => $token,
                ]);
        } catch (ConnectionException $e) {
            // The error text usually contains the URL (and so the API key): mask it, and do not
            // chain the original exception, which would be logged with the key.
            Log::error('Kavenegar: connection failed', [
                'template' => $template,
                'error' => str_replace($apiKey, '***', $e->getMessage()),
            ]);

            throw new SmsException('Could not connect to Kavenegar.');
        }

        // Kavenegar reports the result in return.status (200 = accepted), mirrored in the HTTP status.
        $status = (int) $response->json('return.status', $response->status());

        if ($response->successful() && $status === 200) {
            return;
        }

        Log::error('Kavenegar: send failed', [
            'template' => $template,
            'http_status' => $response->status(),
            'status' => $status,
            'message' => $response->json('return.message'),
        ]);

        throw new SmsException("Kavenegar returned status {$status}.");
    }
}
