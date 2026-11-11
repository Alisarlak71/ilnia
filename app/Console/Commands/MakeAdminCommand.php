<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Create an admin user for the Filament panel (/admin),
 * or promote an existing user to admin.
 *
 *   php artisan ilnia:make-admin
 */
class MakeAdminCommand extends Command
{
    protected $signature = 'ilnia:make-admin';

    protected $description = 'Create an admin user for the admin panel (/admin)';

    public function handle(): int
    {
        $email = text(
            label: 'ایمیل مدیر',
            required: true,
            validate: fn (string $value) => Validator::make(['email' => $value], ['email' => 'email'])->fails()
                ? 'ایمیل معتبر نیست.'
                : null,
        );

        $existing = User::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_admin) {
                $this->info('این کاربر از قبل مدیر است.');

                return self::SUCCESS;
            }

            if (! confirm("کاربری با این ایمیل هست ({$existing->name}). مدیرش کنم؟")) {
                return self::FAILURE;
            }

            $existing->forceFill(['is_admin' => true])->save();
            $this->info('کاربر مدیر شد.');

            return self::SUCCESS;
        }

        $name = text(label: 'نام مدیر', required: true);

        $password = password(
            label: 'رمز عبور (حداقل ۱۲ حرف)',
            required: true,
            validate: fn (string $value) => mb_strlen($value) < 12 ? 'رمز باید حداقل ۱۲ حرف باشد.' : null,
        );

        $user = new User(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        $this->info('مدیر ساخته شد. ورود: '.url('/admin'));

        return self::SUCCESS;
    }
}
