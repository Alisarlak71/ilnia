{{--
    Dark site footer: brand · quick menu · info pages (published ones only) · social accounts.
    Social accounts come from config('shop.social') (.env SOCIAL_*); an empty one is not shown,
    so the footer never links to an account that does not exist yet.
--}}
@php
    $pages = \App\Models\Page::published()->get(['slug', 'title'])
        ->sortBy(fn ($p) => array_search($p->slug, array_keys(\App\Models\Page::SLUGS)));

    $menu = [
        ['url' => route('home'), 'label' => __('site.nav_home')],
        ['url' => route('products.index'), 'label' => __('site.nav_products')],
        ['url' => route('cart'), 'label' => __('site.footer_cart')],
        auth()->check()
            ? ['url' => route('account.orders'), 'label' => __('site.footer_orders')]
            : ['url' => route('login'), 'label' => __('site.footer_login')],
    ];

    $handles = config('shop.social', []);
    $social = array_values(array_filter([
        filled($handles['instagram'] ?? null) ? ['key' => 'instagram', 'url' => 'https://instagram.com/'.rawurlencode($handles['instagram']), 'handle' => $handles['instagram']] : null,
        filled($handles['telegram'] ?? null) ? ['key' => 'telegram', 'url' => 'https://t.me/'.rawurlencode($handles['telegram']), 'handle' => $handles['telegram']] : null,
        filled($handles['bale'] ?? null) ? ['key' => 'bale', 'url' => 'https://ble.ir/'.rawurlencode($handles['bale']), 'handle' => $handles['bale']] : null,
    ]));

    $link = 'text-cream/75 transition-colors hover:text-gold focus-visible:text-gold';
@endphp

<footer class="bg-ink text-cream">
    <div class="h-px bg-linear-to-l from-transparent via-gold/50 to-transparent"></div>

    <div class="mx-auto max-w-6xl px-4 pb-8 pt-14">
        <div class="grid gap-10 text-center sm:grid-cols-2 sm:text-start lg:grid-cols-12 lg:gap-8">
            {{-- Brand --}}
            <div class="flex flex-col items-center gap-3 sm:col-span-2 sm:items-start lg:col-span-4">
                <x-logo :size="40" />
                <p class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold sm:ps-0" dir="ltr">{{ __('site.name_latin') }}</p>
                <p class="text-gold">{{ __('site.slogan_primary') }}</p>
                <p class="max-w-xs text-sm leading-7 text-cream/65">{{ __('site.footer_about') }}</p>
            </div>

            {{-- Quick menu --}}
            <nav class="lg:col-span-2 lg:col-start-6" aria-label="{{ __('site.footer_menu') }}">
                <h2 class="text-sm font-bold text-gold">{{ __('site.footer_menu') }}</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    @foreach ($menu as $item)
                        <li><a href="{{ $item['url'] }}" class="{{ $link }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            {{-- Info pages (only those published in the panel) --}}
            @if ($pages->isNotEmpty())
                <nav class="lg:col-span-2" aria-label="{{ __('site.footer_nav') }}">
                    <h2 class="text-sm font-bold text-gold">{{ __('site.footer_info') }}</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach ($pages as $page)
                            <li><a href="{{ $page->url() }}" class="{{ $link }}">{{ $page->title }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            {{-- Social accounts --}}
            @if ($social)
                <div class="{{ $pages->isNotEmpty() ? 'lg:col-span-3' : 'lg:col-span-3 lg:col-start-10' }}">
                    <h2 class="text-sm font-bold text-gold">{{ __('site.footer_social') }}</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        @foreach ($social as $account)
                            <li>
                                <a href="{{ $account['url'] }}" target="_blank" rel="noopener"
                                   aria-label="{{ __('site.social_open', ['name' => __('site.social_'.$account['key'])]) }}"
                                   class="group inline-flex items-center gap-3 {{ $link }}">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-cream/15 text-cream/80 transition-colors group-hover:border-gold group-hover:text-gold" aria-hidden="true">
                                        @switch($account['key'])
                                            @case('instagram')
                                                <svg viewBox="0 0 24 24" class="size-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.4" cy="6.6" r="1" fill="currentColor" stroke="none"/></svg>
                                                @break
                                            @case('telegram')
                                                <svg viewBox="0 0 24 24" class="size-[18px]" fill="currentColor"><path d="M21.6 4.2 18.4 19.6c-.2 1.1-.9 1.4-1.8.9l-5-3.7-2.4 2.3c-.3.3-.5.5-1 .5l.4-5.1 9.2-8.3c.4-.4-.1-.6-.6-.2L5.8 13.2l-4.9-1.5c-1.1-.3-1.1-1 .2-1.5L20.2 2.8c.9-.3 1.7.2 1.4 1.4z"/></svg>
                                                @break
                                            @case('bale')
                                                <svg viewBox="0 0 24 24" class="size-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3.5c-4.7 0-8.5 3.4-8.5 7.6 0 2.3 1.1 4.3 2.9 5.7l-.6 3.7 3.6-2c.8.2 1.7.3 2.6.3 4.7 0 8.5-3.4 8.5-7.7S16.7 3.5 12 3.5z"/><path d="M8.5 11.2l2.4 2.3 4.6-4.6" stroke-linecap="round"/></svg>
                                                @break
                                        @endswitch
                                    </span>
                                    <span>
                                        <span class="block">{{ __('site.social_'.$account['key']) }}</span>
                                        <span class="block text-xs text-cream/45" dir="ltr">{{ '@'.$account['handle'] }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-cream/10 pt-6 text-xs text-cream/50 sm:flex-row">
            <p>{{ __('site.footer_rights') }}</p>
            <x-ornament tone="light" class="opacity-50" />
        </div>
    </div>
</footer>
