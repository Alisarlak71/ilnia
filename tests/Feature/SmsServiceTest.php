<?php

namespace Tests\Feature;

use App\Exceptions\SmsException;
use App\Services\Sms\KavenegarSmsDriver;
use App\Services\Sms\LogSmsDriver;
use App\Services\Sms\SmsService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    private const KEY = 'TEST-API-KEY-123';

    private function useKavenegar(): SmsService
    {
        config([
            'services.sms.driver' => 'kavenegar',
            'services.kavenegar.api_key' => self::KEY,
            'services.kavenegar.otp_template' => 'otp',
            'services.kavenegar.order_template' => 'order',
        ]);

        return app(SmsService::class);
    }

    private function kavenegarOk(): array
    {
        return ['return' => ['status' => 200, 'message' => 'تایید شد'], 'entries' => [['messageid' => 1, 'status' => 5]]];
    }

    // --- driver selection -------------------------------------------------

    public function test_driver_is_chosen_from_config(): void
    {
        config(['services.sms.driver' => 'log']);
        $this->assertInstanceOf(LogSmsDriver::class, app(SmsService::class));

        config(['services.sms.driver' => 'kavenegar']);
        $this->assertInstanceOf(KavenegarSmsDriver::class, app(SmsService::class));
    }

    public function test_unknown_driver_fails_loudly(): void
    {
        config(['services.sms.driver' => 'pigeon']);

        $this->expectException(InvalidArgumentException::class);
        app(SmsService::class);
    }

    // --- log driver -------------------------------------------------------

    public function test_log_driver_writes_code_to_log_and_sends_nothing(): void
    {
        Http::fake();
        Log::spy();
        config(['services.sms.driver' => 'log']);

        app(SmsService::class)->sendOtp('+98 912 123 4567', '48213');

        Log::shouldHaveReceived('info')->once()->withArgs(fn ($message, $context) => $message === 'SMS (log driver, not sent)'
            && $context === ['to' => '09121234567', 'template' => 'otp', 'token' => '48213']);
        Http::assertNothingSent();
    }

    public function test_log_driver_rejects_invalid_mobile(): void
    {
        config(['services.sms.driver' => 'log']);

        $this->expectException(SmsException::class);
        app(SmsService::class)->sendOtp('12345', '11111');
    }

    public function test_log_driver_in_production_does_not_log_the_code(): void
    {
        Log::spy();
        config(['services.sms.driver' => 'log']);
        $this->app->detectEnvironment(fn () => 'production');

        app(SmsService::class)->sendOtp('09121234567', '48213');

        Log::shouldHaveReceived('warning')->once();
        Log::shouldNotHaveReceived('info');
    }

    // --- kavenegar driver ---------------------------------------------------

    public function test_otp_is_sent_with_template_and_normalized_mobile(): void
    {
        Http::fake(['api.kavenegar.com/*' => Http::response($this->kavenegarOk())]);

        $this->useKavenegar()->sendOtp('۰۹۱۲۱۲۳۴۵۶۷', '48213');

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === 'https://api.kavenegar.com/v1/'.self::KEY.'/verify/lookup.json'
            && $request['receptor'] === '09121234567'
            && $request['template'] === 'otp'
            && $request['token'] === '48213');
    }

    public function test_order_confirmation_uses_order_template(): void
    {
        Http::fake(['api.kavenegar.com/*' => Http::response($this->kavenegarOk())]);

        $this->useKavenegar()->sendOrderConfirmation('09121234567', 'IL-1405-0012');

        Http::assertSent(fn (Request $request) => $request['template'] === 'order' && $request['token'] === 'IL-1405-0012');
    }

    public function test_api_error_status_throws_and_logs_without_key(): void
    {
        Http::fake(['api.kavenegar.com/*' => Http::response(['return' => ['status' => 424, 'message' => 'الگوی مورد نظر پیدا نشد'], 'entries' => null], 424)]);
        Log::spy();

        try {
            $this->useKavenegar()->sendOtp('09121234567', '48213');
            $this->fail('Expected SmsException');
        } catch (SmsException $e) {
            $this->assertStringContainsString('424', $e->getMessage());
            $this->assertStringNotContainsString(self::KEY, $e->getMessage());
        }

        Log::shouldHaveReceived('error')->once()->withArgs(fn ($message, $context) => ! str_contains(json_encode($context, JSON_UNESCAPED_UNICODE), self::KEY)
            && $context['status'] === 424);
    }

    public function test_status_in_body_is_checked_even_when_http_is_200(): void
    {
        Http::fake(['api.kavenegar.com/*' => Http::response(['return' => ['status' => 418, 'message' => 'اعتبار حساب شما کافی نیست'], 'entries' => null], 200)]);

        $this->expectException(SmsException::class);
        $this->useKavenegar()->sendOtp('09121234567', '48213');
    }

    public function test_server_error_throws(): void
    {
        Http::fake(['api.kavenegar.com/*' => Http::response('Bad gateway', 502)]);

        $this->expectException(SmsException::class);
        $this->useKavenegar()->sendOtp('09121234567', '48213');
    }

    public function test_connection_failure_throws_and_never_leaks_api_key(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28: timed out for https://api.kavenegar.com/v1/'.self::KEY.'/verify/lookup.json'));
        Log::spy();

        try {
            $this->useKavenegar()->sendOtp('09121234567', '48213');
            $this->fail('Expected SmsException');
        } catch (SmsException $e) {
            $this->assertStringNotContainsString(self::KEY, $e->getMessage());
            $this->assertNull($e->getPrevious(), 'The original exception (with the key in its message) must not be chained.');
        }

        Log::shouldHaveReceived('error')->once()->withArgs(fn ($message, $context) => ! str_contains($context['error'], self::KEY)
            && str_contains($context['error'], '***'));
    }

    public function test_missing_api_key_throws_without_request(): void
    {
        Http::fake();
        config(['services.sms.driver' => 'kavenegar', 'services.kavenegar.api_key' => null]);

        try {
            app(SmsService::class)->sendOtp('09121234567', '48213');
            $this->fail('Expected SmsException');
        } catch (SmsException) {
            Http::assertNothingSent();
        }
    }

    public function test_invalid_mobile_or_token_with_spaces_throws_without_request(): void
    {
        Http::fake();
        $sms = $this->useKavenegar();

        foreach ([['02112345678', '11111'], ['09121234567', '11 111'], ['09121234567', '']] as [$mobile, $token]) {
            try {
                $sms->sendOtp($mobile, $token);
                $this->fail("Expected SmsException for {$mobile} / '{$token}'");
            } catch (SmsException) {
                // expected
            }
        }

        Http::assertNothingSent();
    }
}
