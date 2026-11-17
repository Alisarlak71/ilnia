{{--
    Phone bottom bar (hidden from tablet width up): Home · Products · Cart.
    Fixed to the foot of the screen, above the iPhone home indicator (safe-area). The layout adds the
    same height as bottom padding, so the footer is never hidden behind it.
--}}
@php
    $tab = 'flex h-full flex-col items-center justify-center gap-1 text-[0.7rem] transition-colors';
    $homeActive = request()->routeIs('home');
    $productsActive = request()->routeIs('products.*');
@endphp

<nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gold/25 bg-ink/95 pb-[env(safe-area-inset-bottom)] text-cream shadow-[0_-8px_24px_-12px_rgba(0,0,0,0.5)] backdrop-blur md:hidden"
     aria-label="{{ __('site.bottom_nav') }}">
    <ul class="grid h-16 grid-cols-3">
        <li>
            <a href="{{ route('home') }}" @class([$tab, 'text-gold' => $homeActive, 'text-cream/70' => ! $homeActive])
               @if ($homeActive) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20h-5.5v-5.5h-5V20H4z"/></svg>
                <span>{{ __('site.nav_home') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('products.index') }}" @class([$tab, 'text-gold' => $productsActive, 'text-cream/70' => ! $productsActive])
               @if ($productsActive) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/></svg>
                <span>{{ __('site.nav_products') }}</span>
            </a>
        </li>
        <li>
            <livewire:cart-icon variant="tab" />
        </li>
    </ul>
</nav>
