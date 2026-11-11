{{-- Dark site header: logo, main menu, login/logout and cart icon (Livewire, updates live). --}}
@php
    $links = [
        ['route' => 'home', 'label' => __('site.nav_home'), 'active' => request()->routeIs('home')],
        ['route' => 'products.index', 'label' => __('site.nav_products'), 'active' => request()->routeIs('products.*')],
    ];
@endphp

<header class="bg-ink text-cream">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
        <x-logo />

        <nav class="ms-auto" aria-label="{{ __('site.main_nav') }}">
            <ul class="flex items-center gap-5 text-sm sm:gap-6">
                @foreach ($links as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                           @class(['transition-colors hover:text-gold', 'text-gold' => $link['active']])
                           @if ($link['active']) aria-current="page" @endif>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm transition-colors hover:text-gold">{{ __('otp.logout') }}</button>
            </form>
        @else
            <a href="{{ route('login') }}"
               @class(['text-sm transition-colors hover:text-gold', 'text-gold' => request()->routeIs('login')])>{{ __('otp.login') }}</a>
        @endauth

        <livewire:cart-icon />
    </div>
    <div class="h-px bg-linear-to-l from-transparent via-gold/50 to-transparent"></div>
</header>
