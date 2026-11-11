<?php

namespace Tests\Feature;

use App\Exceptions\OtpException;
use App\Livewire\LoginPage;
use App\Models\OtpCode;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use App\Services\OtpService;
use App\Services\Sms\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\Support\FakeSmsService;
use Tests\TestCase;

class OtpLoginTest extends TestCase
{
    use RefreshDatabase;

    private FakeSmsService $sms;

    private OtpService $otp;

    private const IP = '10.0.0.1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->sms = new FakeSmsService;
        $this->app->instance(SmsService::class, $this->sms);
        $this->otp = app(OtpService::class);
    }

    private function assertOtpError(callable $action, string $contains): void
    {
        try {
            $action();
            $this->fail("Expected OtpException containing «{$contains}»");
        } catch (OtpException $e) {
            $this->assertStringContainsString($contains, $e->getMessage());
        }
    }

    // --- OtpService: sending ------------------------------------------------

    public function test_send_normalizes_mobile_and_stores_only_a_hash(): void
    {
        $mobile = $this->otp->send('+98 912 123 4567', self::IP);

        $this->assertSame('09121234567', $mobile);
        $code = $this->sms->lastCode();
        $this->assertMatchesRegularExpression('/^\d{5}$/', $code);
        $this->assertSame('09121234567', $this->sms->sent[0]['mobile']);

        $row = OtpCode::sole();
        $this->assertNotSame($code, $row->code_hash);
        $this->assertTrue(Hash::check($code, $row->code_hash));
        $this->assertDatabaseMissing('otp_codes', ['code_hash' => $code]);
    }

    public function test_invalid_mobile_is_rejected_without_sms(): void
    {
        $this->assertOtpError(fn () => $this->otp->send('12345', self::IP), 'شمارهٔ موبایل معتبر نیست');
        $this->assertSame([], $this->sms->sent);
    }

    public function test_resend_must_wait_for_cooldown(): void
    {
        $this->otp->send('09121234567', self::IP);

        $this->assertOtpError(fn () => $this->otp->send('09121234567', self::IP), 'ثانیه دیگر صبر کنید');

        $this->travel(OtpService::RESEND_AFTER + 1)->seconds();
        $this->otp->send('09121234567', self::IP);

        $this->assertCount(2, $this->sms->sent);
        $this->assertSame(1, OtpCode::count(), 'A new code replaces the old one.');
    }

    public function test_hourly_send_limit_per_mobile(): void
    {
        for ($i = 0; $i < OtpService::MAX_SENDS_PER_MOBILE; $i++) {
            $this->otp->send('09121234567', '10.0.0.'.$i);
            $this->travel(OtpService::RESEND_AFTER + 1)->seconds();
        }

        $this->assertOtpError(fn () => $this->otp->send('09121234567', '10.0.1.1'), 'بیش از حد مجاز');
    }

    public function test_hourly_send_limit_per_ip(): void
    {
        for ($i = 0; $i < OtpService::MAX_SENDS_PER_IP; $i++) {
            $this->otp->send('0912000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT), self::IP);
        }

        $this->assertOtpError(fn () => $this->otp->send('09129999999', self::IP), 'بیش از حد مجاز');
    }

    public function test_sms_failure_removes_the_code(): void
    {
        $this->sms->fail = true;

        $this->assertOtpError(fn () => $this->otp->send('09121234567', self::IP), 'ارسال پیامک ممکن نشد');
        $this->assertSame(0, OtpCode::count());
    }

    // --- OtpService: verifying ----------------------------------------------

    public function test_correct_code_creates_user_and_uses_up_the_code(): void
    {
        $this->otp->send('09121234567', self::IP);

        $user = $this->otp->verify('09121234567', $this->sms->lastCode(), self::IP);

        $this->assertSame('09121234567', $user->mobile);
        $this->assertFalse($user->is_admin);
        $this->assertSame(0, OtpCode::count());

        // Code cannot be reused.
        $this->assertOtpError(fn () => $this->otp->verify('09121234567', $this->sms->lastCode(), self::IP), 'منقضی');
    }

    public function test_existing_user_is_reused(): void
    {
        $existing = User::create(['mobile' => '09121234567', 'name' => 'مشتری']);
        $this->otp->send('09121234567', self::IP);

        $user = $this->otp->verify('09121234567', $this->sms->lastCode(), self::IP);

        $this->assertTrue($user->is($existing));
        $this->assertSame(1, User::count());
    }

    public function test_persian_digits_in_code_are_accepted(): void
    {
        $this->otp->send('09121234567', self::IP);
        $persian = \App\Support\Digits::toPersian($this->sms->lastCode());

        $this->assertSame('09121234567', $this->otp->verify('۰۹۱۲۱۲۳۴۵۶۷', $persian, self::IP)->mobile);
    }

    public function test_wrong_code_counts_attempts_and_locks_after_max(): void
    {
        $this->otp->send('09121234567', self::IP);
        $wrong = $this->sms->lastCode() === '11111' ? '22222' : '11111';

        $this->assertOtpError(fn () => $this->otp->verify('09121234567', $wrong, self::IP), '۴ بار دیگر');

        for ($i = 2; $i <= OtpService::MAX_ATTEMPTS; $i++) {
            try {
                $this->otp->verify('09121234567', $wrong, self::IP);
            } catch (OtpException) {
            }
        }

        // Even the correct code no longer works.
        $this->assertOtpError(fn () => $this->otp->verify('09121234567', $this->sms->lastCode(), self::IP), 'کد تازه بگیرید');
        $this->assertSame(0, User::count());
    }

    public function test_expired_code_is_rejected(): void
    {
        $this->otp->send('09121234567', self::IP);
        $this->travel(OtpService::CODE_TTL + 1)->seconds();

        $this->assertOtpError(fn () => $this->otp->verify('09121234567', $this->sms->lastCode(), self::IP), 'منقضی');
    }

    public function test_code_for_another_mobile_does_not_work(): void
    {
        $this->otp->send('09121234567', self::IP);

        $this->assertOtpError(fn () => $this->otp->verify('09351234567', $this->sms->lastCode(), self::IP), 'منقضی');
    }

    public function test_malformed_code_is_rejected(): void
    {
        $this->otp->send('09121234567', self::IP);

        $this->assertOtpError(fn () => $this->otp->verify('09121234567', '12', self::IP), 'کامل وارد کنید');
        $this->assertSame(0, OtpCode::sole()->attempts, 'A malformed code does not use up a try.');
    }

    public function test_verify_limit_per_ip(): void
    {
        for ($i = 0; $i < OtpService::MAX_VERIFIES_PER_IP; $i++) {
            try {
                $this->otp->verify('09121234567', '00000', self::IP);
            } catch (OtpException) {
            }
        }

        $this->assertOtpError(fn () => $this->otp->verify('09121234567', '00000', self::IP), 'بیش از حد مجاز');
    }

    public function test_old_codes_are_pruned(): void
    {
        $this->otp->send('09121234567', self::IP);
        $this->travel(2)->days();

        $this->artisan('model:prune', ['--model' => [OtpCode::class]])->assertSuccessful();

        $this->assertSame(0, OtpCode::count());
    }

    // --- Login page -----------------------------------------------------------

    public function test_login_page_loads_and_header_links_to_it(): void
    {
        $this->get('/login')->assertOk()->assertSee('<title>ورود به حساب | ایلنیا</title>', false);
        $this->get('/')->assertSee('href="'.route('login').'"', false);
    }

    public function test_full_login_flow_logs_in_and_keeps_cart(): void
    {
        $variant = ProductVariant::factory()->create(['price' => 480000, 'stock' => 5]);
        app(CartService::class)->add($variant->id, 2);

        $component = Livewire::test(LoginPage::class)
            ->set('mobile', '۰۹۱۲۱۲۳۴۵۶۷')
            ->call('sendCode')
            ->assertSet('sentTo', '09121234567')
            ->assertSee('پیامک شد');

        $component->set('code', $this->sms->lastCode())
            ->call('verify')
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertSame('09121234567', auth()->user()->mobile);
        $this->assertSame(2, app(CartService::class)->count(), 'The guest cart survives login.');
    }

    public function test_login_redirects_to_intended_page(): void
    {
        session()->put('url.intended', url('/cart'));

        $component = Livewire::test(LoginPage::class)->set('mobile', '09121234567')->call('sendCode');
        $component->set('code', $this->sms->lastCode())->call('verify')->assertRedirect(url('/cart'));
    }

    public function test_wrong_code_shows_error_and_does_not_log_in(): void
    {
        $component = Livewire::test(LoginPage::class)->set('mobile', '09121234567')->call('sendCode');
        $wrong = $this->sms->lastCode() === '11111' ? '22222' : '11111';

        $component->set('code', $wrong)->call('verify')->assertSee('کد اشتباه است')->assertSet('code', '');

        $this->assertGuest();
    }

    public function test_sent_to_cannot_be_changed_from_the_browser(): void
    {
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::test(LoginPage::class)->set('sentTo', '09121234567');
    }

    public function test_change_mobile_goes_back_to_step_one(): void
    {
        Livewire::test(LoginPage::class)
            ->set('mobile', '09121234567')
            ->call('sendCode')
            ->call('changeMobile')
            ->assertSet('sentTo', null)
            ->assertSee('دریافت کد');
    }

    public function test_logged_in_user_is_redirected_away_from_login(): void
    {
        $this->actingAs(User::create(['mobile' => '09121234567']));

        $this->get('/login')->assertRedirect();
    }

    public function test_logout(): void
    {
        $this->actingAs(User::create(['mobile' => '09121234567']));

        $this->get('/')->assertSee('خروج');
        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_logout_requires_post_and_csrf_route_is_protected(): void
    {
        $this->get('/logout')->assertMethodNotAllowed();
    }

    public function test_otp_login_does_not_grant_admin_panel(): void
    {
        $this->otp->send('09121234567', self::IP);
        $user = $this->otp->verify('09121234567', $this->sms->lastCode(), self::IP);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
