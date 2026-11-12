<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Pre-launch / production check of the settings in .env (docs/SECURITY.md).
 * Run it on the host after every change to .env:   php artisan ilnia:security-check
 *
 *   ✗ FAIL  must be fixed before going live (exit code 1)
 *   ! WARN  should be looked at
 *   ✓ OK
 *
 * It only reads settings: it never prints secret values.
 */
#[Signature('ilnia:security-check')]
#[Description('Check production security settings (.env) before launch')]
class SecurityCheckCommand extends Command
{
    private int $failures = 0;

    public function handle(): int
    {
        $this->line('بررسی تنظیمات امنیتی ایلنیا');
        $this->newLine();

        $this->check(app()->isProduction(), 'APP_ENV=production', 'APP_ENV باید production باشد.');
        $this->check(! config('app.debug'), 'APP_DEBUG=false', 'APP_DEBUG باید false باشد؛ وگرنه جزئیات خطا و تنظیمات به بازدیدکننده نشان داده می‌شود.');
        $this->check(filled(config('app.key')), 'APP_KEY ساخته شده', 'APP_KEY خالی است. یک بار php artisan key:generate را اجرا کنید.');
        $this->check(str_starts_with((string) config('app.url'), 'https://'), 'APP_URL با https', 'APP_URL باید با https:// شروع شود (نشانی واقعی سایت).');
        $this->check((bool) config('session.secure'), 'SESSION_SECURE_COOKIE=true', 'SESSION_SECURE_COOKIE=true بگذارید تا کوکی ورود فقط روی HTTPS فرستاده شود.');

        if (config('services.sms.driver') === 'screen') {
            $this->check(false, '', 'SMS_DRIVER=screen است: کد ورود روی صفحه نمایش داده می‌شود و هر کسی با هر شماره‌ای وارد می‌شود. قبل از پذیرفتن مشتری واقعی، کاوه‌نگار را فعال کنید.');
        }

        $this->check(
            config('services.sms.driver') === 'kavenegar' && filled(config('services.kavenegar.api_key')),
            'پیامک: کاوه‌نگار',
            'SMS_DRIVER باید kavenegar و KAVENEGAR_API_KEY پر باشد؛ وگرنه کد ورود به مشتری نمی‌رسد.',
        );

        if (filled(config('services.zarinpal.merchant_id'))) {
            $this->check(! config('services.zarinpal.sandbox'), 'زرین‌پال: حالت واقعی', 'ZARINPAL_SANDBOX باید false باشد؛ در حالت sandbox پول واقعی دریافت نمی‌شود.');
        } else {
            $this->warn('  ! پرداخت آنلاین خاموش است (ZARINPAL_MERCHANT_ID خالی). تا وقتی درگاه نگرفته‌اید، درست است.');
        }

        $admins = User::where('is_admin', true)->count();
        $this->check($admins > 0, 'مدیر پنل: '.$admins.' نفر', 'هیچ مدیری وجود ندارد. با php artisan ilnia:make-admin بسازید.');
        if ($admins > 3) {
            $this->warn("  ! {$admins} مدیر وجود دارد. مطمئن شوید همه را می‌شناسید.");
        }

        $this->advise(config('logging.channels.single.level') !== 'debug', 'LOG_LEVEL', 'LOG_LEVEL=error یا warning بگذارید تا لاگ پر نشود.');
        $this->advise(config('queue.default') !== 'sync', 'صف: '.config('queue.default'), 'QUEUE_CONNECTION=database بگذارید تا پیامک تأیید سفارش صفحه را کند نکند.');
        $this->advise(! in_array(config('session.driver'), ['array', 'cookie'], true), 'SESSION_DRIVER: '.config('session.driver'), 'SESSION_DRIVER=database بگذارید.');
        $this->advise(is_link(public_path('storage')) || is_dir(public_path('storage')), 'public/storage', 'عکس‌های آپلودی نمایش داده نمی‌شوند. php artisan storage:link را اجرا کنید (یا راه جایگزین در docs/DEPLOY.md).');
        $this->check(! file_exists(public_path('.env')), 'فایل .env بیرون از public', 'فایل .env داخل پوشهٔ public است و از اینترنت خوانده می‌شود! فوراً جابه‌جایش کنید.');

        $this->newLine();
        if ($this->failures > 0) {
            $this->error("{$this->failures} مورد باید قبل از انتشار درست شود.");

            return self::FAILURE;
        }

        $this->info('همهٔ موارد ضروری درست است.');

        return self::SUCCESS;
    }

    private function check(bool $ok, string $label, string $fix): void
    {
        if ($ok) {
            $this->line("  <fg=green>✓</> {$label}");

            return;
        }

        $this->failures++;
        $this->line("  <fg=red>✗ {$fix}</>");
    }

    private function advise(bool $ok, string $label, string $fix): void
    {
        $ok ? $this->line("  <fg=green>✓</> {$label}") : $this->warn("  ! {$fix}");
    }
}
