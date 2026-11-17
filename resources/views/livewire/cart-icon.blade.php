@php
    $active = request()->routeIs('cart', 'checkout');
    $label = __('cart.icon_label', ['count' => \App\Support\Digits::toPersian($count)]);
@endphp

@if ($variant === 'tab')
    {{-- Phone bottom bar: icon over a label, gold when on the cart. --}}
    <a href="{{ route('cart') }}" aria-label="{{ $label }}" @if ($active) aria-current="page" @endif
       @class(['flex h-full flex-col items-center justify-center gap-1 text-[0.7rem] transition-colors', 'text-gold' => $active, 'text-cream/70' => ! $active])>
        <span class="relative">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 7h12l-1 13H7L6 7z"/>
                <path d="M9 7V6a3 3 0 0 1 6 0v1"/>
            </svg>
            @if ($count > 0)
                <span class="absolute -end-2.5 -top-1.5 min-w-5 rounded-full bg-gold px-1 text-center text-[0.7rem] font-bold leading-5 text-ink">
                    {{ \App\Support\Digits::toPersian($count) }}
                </span>
            @endif
        </span>
        <span aria-hidden="true">{{ __('site.footer_cart') }}</span>
    </a>
@else
    <a href="{{ route('cart') }}"
       class="relative inline-flex items-center gap-2 rounded-btn border border-cream/15 px-3 py-2 transition-colors hover:border-gold hover:text-gold {{ $active ? 'border-gold text-gold' : '' }}"
       aria-label="{{ $label }}"
       @if ($active) aria-current="page" @endif>
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M6 7h12l-1 13H7L6 7z"/>
            <path d="M9 7V6a3 3 0 0 1 6 0v1"/>
        </svg>
        <span class="text-sm" aria-hidden="true">{{ __('site.footer_cart') }}</span>
        @if ($count > 0)
            <span class="min-w-5 rounded-full bg-gold px-1 text-center text-xs font-bold leading-5 text-ink">
                {{ \App\Support\Digits::toPersian($count) }}
            </span>
        @endif
    </a>
@endif
