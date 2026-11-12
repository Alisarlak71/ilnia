<?php

namespace Tests\Feature;

use App\Livewire\LoginPage;
use App\Models\User;
use App\Services\Sms\ScreenSmsDriver;
use App\Services\Sms\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** SMS_DRIVER=screen: temporary mode, the login code is shown on the login page. */
class ScreenSmsDriverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.sms.driver' => 'screen']);
    }

    public function test_driver_is_selected_from_config(): void
    {
        $this->assertInstanceOf(ScreenSmsDriver::class, app(SmsService::class));
    }

    public function test_login_page_shows_the_code_and_it_logs_in(): void
    {
        $component = Livewire::test(LoginPage::class)
            ->set('mobile', '۰۹۱۲۱۲۳۴۵۶۷')
            ->call('sendCode')
            ->assertSet('sentTo', '09121234567')
            ->assertSee('کد ورود شما:')
            ->assertSee('حالت آزمایشی');

        $code = $component->get('screenCode');
        $this->assertMatchesRegularExpression('/^\d{5}$/', $code);
        $component->assertSee($code);

        $component->set('code', $code)->call('verify')->assertRedirect(route('home'));
        $this->assertAuthenticatedAs(User::where('mobile', '09121234567')->sole());
    }

    public function test_code_is_not_shown_with_other_drivers(): void
    {
        config(['services.sms.driver' => 'log']);

        Livewire::test(LoginPage::class)
            ->set('mobile', '09121234567')
            ->call('sendCode')
            ->assertSet('screenCode', null)
            ->assertDontSee('کد ورود شما:')
            ->assertSee('کد');
    }

    public function test_code_cannot_be_set_from_the_browser(): void
    {
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::test(LoginPage::class)->set('screenCode', '12345');
    }

    public function test_changing_mobile_hides_the_code(): void
    {
        Livewire::test(LoginPage::class)
            ->set('mobile', '09121234567')
            ->call('sendCode')
            ->call('changeMobile')
            ->assertSet('screenCode', null)
            ->assertDontSee('کد ورود شما:');
    }

    public function test_order_confirmation_does_not_fail(): void
    {
        app(SmsService::class)->sendOrderConfirmation('09121234567', 'IL-050821-0001');

        $this->assertTrue(true); // only logged, no exception
    }

    public function test_security_check_reports_screen_mode_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('ilnia:security-check')
            ->expectsOutputToContain('SMS_DRIVER=screen است')
            ->assertFailed();
    }
}
