<div class="mx-auto max-w-md px-4 py-12 sm:py-16">
    <div class="rounded-card border border-line bg-paper p-6 sm:p-8">
        <div class="text-center">
            <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="56" height="56" alt="" class="mx-auto">
            <h1 class="mt-4 text-2xl font-extrabold">{{ __('otp.title') }}</h1>
        </div>

        @if ($error)
            <p role="alert" class="mt-6 rounded-btn border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">{{ $error }}</p>
        @endif

        @if ($sentTo === null)
            {{-- Step 1: mobile --}}
            <form wire:submit="sendCode" class="mt-6">
                <p class="text-sm text-muted">{{ __('otp.intro') }}</p>
                <label for="mobile" class="mt-5 block font-bold">{{ __('otp.mobile_label') }}</label>
                <input id="mobile" type="tel" wire:model="mobile" inputmode="tel" autocomplete="tel" dir="ltr"
                       placeholder="09121234567" required autofocus
                       class="mt-2 w-full rounded-btn border border-line bg-cream px-4 py-3 text-center text-lg tracking-wider focus:border-gold-deep focus:outline-none focus:ring-2 focus:ring-honey/40">
                <button type="submit" wire:loading.attr="disabled"
                        class="mt-5 w-full rounded-btn border border-ink bg-ink px-6 py-3 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60">
                    {{ __('otp.send_code') }}
                </button>
                <p class="mt-4 text-center text-xs text-muted">{{ __('otp.no_password') }}</p>
            </form>
        @else
            {{-- Step 2: code --}}
            <form wire:submit="verify" class="mt-6">
                <p role="status" class="text-sm text-muted">
                    {{ __('otp.code_sent', ['length' => \App\Support\Digits::toPersian($codeLength), 'mobile' => \App\Support\Digits::toPersian($sentTo)]) }}
                </p>
                <label for="code" class="mt-5 block font-bold">{{ __('otp.code_label') }}</label>
                <input id="code" type="text" wire:model="code" inputmode="numeric" autocomplete="one-time-code" dir="ltr"
                       maxlength="{{ $codeLength }}" required autofocus
                       class="mt-2 w-full rounded-btn border border-line bg-cream px-4 py-3 text-center text-2xl tracking-[0.5em] focus:border-gold-deep focus:outline-none focus:ring-2 focus:ring-honey/40">
                <button type="submit" wire:loading.attr="disabled"
                        class="mt-5 w-full rounded-btn border border-ink bg-ink px-6 py-3 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60">
                    {{ __('otp.verify') }}
                </button>
            </form>

            <div class="mt-5 flex items-center justify-between text-sm">
                <button type="button" wire:click="changeMobile" class="text-gold-deep hover:underline">{{ __('otp.change_mobile') }}</button>

                {{-- Countdown runs in the browser; the server still enforces the wait. --}}
                <div x-data="{ left: {{ $resendIn }}, t: null }"
                     x-init="t = setInterval(() => { if (left > 0) left-- }, 1000)"
                     wire:key="resend-{{ $sentTo }}-{{ $resendIn }}">
                    <button type="button" wire:click="resend" x-show="left === 0" x-cloak class="text-gold-deep hover:underline">{{ __('otp.resend') }}</button>
                    <span x-show="left > 0" class="text-muted"
                          x-text="'{{ __('otp.resend_in', ['seconds' => '__S__']) }}'.replace('__S__', left.toLocaleString('fa-IR'))">
                        {{ __('otp.resend_in', ['seconds' => \App\Support\Digits::toPersian($resendIn)]) }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>
