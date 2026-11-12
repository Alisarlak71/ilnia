{{--
    Dark site footer: brand column · shop links · content pages (only those published in the panel).
    No contact details here until they are real (honesty rule); they live on the contact page.
--}}
@php
    $pages = \App\Models\Page::published()->get(['slug', 'title'])
        ->sortBy(fn ($p) => array_search($p->slug, array_keys(\App\Models\Page::SLUGS)));

    $shopLinks = [
        ['url' => route('products.index'), 'label' => __('site.nav_products')],
        ['url' => route('cart'), 'label' => __('site.footer_cart')],
        ['url' => auth()->check() ? route('account.orders') : route('login'), 'label' => __('site.footer_orders')],
    ];
@endphp

<footer class="relative overflow-hidden bg-ink text-cream">
    <div class="h-px bg-linear-to-l from-transparent via-gold/50 to-transparent"></div>

    {{-- Large, very faint mark as a watermark --}}
    <img src="{{ asset('brand/logo-mark-gold.svg') }}" alt="" aria-hidden="true"
         class="pointer-events-none absolute -bottom-24 -end-24 w-96 opacity-[0.04]">

    <div class="relative mx-auto max-w-6xl px-4 pb-8 pt-16 sm:pt-20">
        <div class="grid gap-12 text-center sm:grid-cols-2 sm:text-start lg:grid-cols-12">
            <div class="flex flex-col items-center gap-4 sm:col-span-2 sm:items-start lg:col-span-6">
                <x-logo :size="44" />
                <p class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold sm:ps-0" dir="ltr">{{ __('site.name_latin') }}</p>
                <p class="max-w-sm text-sm leading-8 text-cream/65">{{ __('site.footer_about') }}</p>
                <p class="mt-2 text-2xl font-black text-gold">{{ __('site.slogan_campaign') }}</p>
            </div>

            <nav class="lg:col-span-3" aria-label="{{ __('site.footer_shop') }}">
                <p class="text-xs font-bold tracking-wide text-gold">{{ __('site.footer_shop') }}</p>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($shopLinks as $link)
                        <li><a href="{{ $link['url'] }}" class="text-cream/75 transition-colors hover:text-gold">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            @if ($pages->isNotEmpty())
                <nav class="lg:col-span-3" aria-label="{{ __('site.footer_nav') }}">
                    <p class="text-xs font-bold tracking-wide text-gold">{{ __('site.footer_info') }}</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($pages as $page)
                            <li><a href="{{ $page->url() }}" class="text-cream/75 transition-colors hover:text-gold">{{ $page->title }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>

        <div class="mt-16 flex flex-col items-center justify-between gap-4 border-t border-cream/10 pt-6 text-xs text-cream/45 sm:flex-row">
            <p>{{ __('site.footer_rights') }}</p>
            <x-ornament tone="light" class="opacity-50" />
            <p>{{ __('site.slogan_primary') }}</p>
        </div>
    </div>
</footer>
