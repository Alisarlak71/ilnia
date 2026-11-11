<?php

namespace App\Livewire;

use App\Exceptions\OtpException;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Customer login (/login): step 1 mobile number → step 2 one-time code.
 * All checks and limits live in OtpService.
 */
#[Title('ورود به حساب')]
class LoginPage extends Component
{
    public string $mobile = '';

    public string $code = '';

    /** Normalized mobile the code was sent to; set only by the server. */
    #[Locked]
    public ?string $sentTo = null;

    public ?string $error = null;

    public function sendCode(OtpService $otp): void
    {
        $this->error = null;

        try {
            $this->sentTo = $otp->send($this->mobile, (string) request()->ip());
            $this->code = '';
        } catch (OtpException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function resend(OtpService $otp): void
    {
        if ($this->sentTo === null) {
            return;
        }

        $this->mobile = $this->sentTo;
        $this->sendCode($otp);
    }

    public function verify(OtpService $otp)
    {
        $this->error = null;

        if ($this->sentTo === null) {
            return null;
        }

        try {
            $user = $otp->verify($this->sentTo, $this->code, (string) request()->ip());
        } catch (OtpException $e) {
            $this->error = $e->getMessage();
            $this->code = '';

            return null;
        }

        Auth::login($user, remember: true);
        session()->regenerate();

        return $this->redirectIntended(route('home'), navigate: false);
    }

    public function changeMobile(): void
    {
        $this->sentTo = null;
        $this->code = '';
        $this->error = null;
    }

    public function render(OtpService $otp): View
    {
        return view('livewire.login-page', [
            'resendIn' => $this->sentTo ? $otp->secondsUntilResend($this->sentTo) : 0,
            'codeLength' => OtpService::CODE_LENGTH,
        ]);
    }
}
