<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_login_page_is_persian_and_rtl(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('lang="fa"', false);
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('ایلنیا');
        $response->assertSee('fonts/admin.css', false);
    }

    public function test_non_admin_user_cannot_open_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_user_can_open_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_is_admin_cannot_be_mass_assigned(): void
    {
        $user = User::create([
            'name' => 'کاربر',
            'email' => 'user@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
        ]);

        $this->assertFalse($user->fresh()->is_admin);
    }

    public function test_make_admin_command_creates_an_admin(): void
    {
        $this->artisan('ilnia:make-admin')
            ->expectsQuestion('ایمیل مدیر', 'admin@example.com')
            ->expectsQuestion('نام مدیر', 'مدیر')
            ->expectsQuestion('رمز عبور (حداقل ۱۲ حرف)', 'a-long-secret-1')
            ->assertSuccessful();

        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue($admin->canAccessPanel(filament()->getPanel('admin')));
    }

    public function test_make_admin_command_promotes_existing_user(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->artisan('ilnia:make-admin')
            ->expectsQuestion('ایمیل مدیر', 'old@example.com')
            ->expectsConfirmation("کاربری با این ایمیل هست ({$user->name}). مدیرش کنم؟", 'yes')
            ->assertSuccessful();

        $this->assertTrue($user->fresh()->is_admin);
    }
}
