{{-- Dark site header. The cart icon is added here in Phase 2. --}}
<header class="bg-ink text-cream">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
        <x-logo />

        <nav aria-label="{{ __('site.main_nav') }}">
            <ul class="flex items-center gap-6 text-sm">
                <li>
                    <a href="{{ route('home') }}"
                       class="transition-colors hover:text-gold {{ request()->routeIs('home') ? 'text-gold' : '' }}"
                       @if (request()->routeIs('home')) aria-current="page" @endif>
                        {{ __('site.nav_home') }}
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="h-px bg-gradient-to-l from-transparent via-gold/50 to-transparent"></div>
</header>
