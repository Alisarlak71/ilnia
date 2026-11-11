{{-- Dark site header. The cart icon is added here in Phase 2. --}}
@php
    $links = [
        ['route' => 'home', 'label' => __('site.nav_home'), 'active' => request()->routeIs('home')],
        ['route' => 'products.index', 'label' => __('site.nav_products'), 'active' => request()->routeIs('products.*')],
    ];
@endphp

<header class="bg-ink text-cream">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
        <x-logo />

        <nav aria-label="{{ __('site.main_nav') }}">
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
    </div>
    <div class="h-px bg-gradient-to-l from-transparent via-gold/50 to-transparent"></div>
</header>
