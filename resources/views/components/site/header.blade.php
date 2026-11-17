{{--
    Site header: a thin announcement strip (free shipping, from the panel's shipping settings), then the
    dark bar with logo, main menu, account and cart. From tablet width up the bar stays at the top while
    scrolling. On phones the menu and cart live in the bottom bar (<x-site.bottom-nav />), so the top
    bar only keeps the logo and the account link.
--}}
@php
    $links = [
        ['route' => 'home', 'label' => __('site.nav_home'), 'active' => request()->routeIs('home')],
        ['route' => 'products.index', 'label' => __('site.nav_products'), 'active' => request()->routeIs('products.*')],
    ];

    $freeOver = app(\App\Services\ShippingCalculator::class)->defaultMethod()?->free_over_weight_grams;
    $accountActive = request()->routeIs('account.*', 'login');
@endphp

<div class="bg-gold-deep text-cream">
    <p class="mx-auto flex max-w-6xl items-center justify-center gap-2 px-4 py-1.5 text-center text-xs sm:text-sm">
        @if ($freeOver)
            <svg viewBox="0 0 24 24" class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17" cy="17.5" r="1.6"/></svg>
            {{ __('home.hero_fact', ['weight' => \App\Support\Weight::kg($freeOver)]) }}
        @else
            {{ __('site.slogan_campaign') }}
        @endif
    </p>
</div>

<header class="z-40 bg-ink text-cream md:sticky md:top-0">
    <div class="mx-auto flex max-w-6xl items-center gap-4 px-4 py-3 md:gap-8 md:py-4">
        <x-logo :size="38" />

        <nav class="hidden md:block" aria-label="{{ __('site.main_nav') }}">
            <ul class="flex items-center gap-1 text-sm">
                @foreach ($links as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                           @class(['block rounded-btn px-4 py-2 transition-colors hover:bg-ink-soft hover:text-gold', 'bg-ink-soft text-gold' => $link['active']])
                           @if ($link['active']) aria-current="page" @endif>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="ms-auto flex items-center gap-2 sm:gap-3">
            <a href="{{ auth()->check() ? route('account.orders') : route('login') }}"
               @class(['inline-flex items-center gap-2 rounded-btn px-2 py-2 text-sm transition-colors hover:text-gold sm:px-3', 'text-gold' => $accountActive])
               @if ($accountActive) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" class="size-[22px]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8.5" r="3.8"/><path d="M4.5 20c1.2-3.6 4-5.5 7.5-5.5s6.3 1.9 7.5 5.5"/></svg>
                <span>{{ auth()->check() ? __('account.my_account') : __('otp.login') }}</span>
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-1 py-2 text-sm text-cream/60 transition-colors hover:text-gold">{{ __('otp.logout') }}</button>
                </form>
            @endauth

            <div class="hidden md:block">
                <livewire:cart-icon />
            </div>
        </div>
    </div>
    <div class="h-px bg-linear-to-l from-transparent via-gold/50 to-transparent"></div>
</header>
