<a href="{{ route('cart') }}"
   class="relative inline-flex items-center p-1 transition-colors hover:text-gold {{ request()->routeIs('cart') ? 'text-gold' : '' }}"
   aria-label="{{ __('cart.icon_label', ['count' => \App\Support\Digits::toPersian($count)]) }}"
   @if (request()->routeIs('cart')) aria-current="page" @endif>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M6 7h12l-1 13H7L6 7z"/>
        <path d="M9 7V6a3 3 0 0 1 6 0v1"/>
    </svg>
    @if ($count > 0)
        <span class="absolute -end-2 -top-1 min-w-5 rounded-full bg-gold px-1 text-center text-xs font-bold leading-5 text-ink">
            {{ \App\Support\Digits::toPersian($count) }}
        </span>
    @endif
</a>
