{{-- Dark site footer. Content-page links appear only for pages published in the panel. --}}
@php($pages = \App\Models\Page::published()->get(['slug', 'title'])->sortBy(fn ($p) => array_search($p->slug, array_keys(\App\Models\Page::SLUGS))))
<footer class="bg-ink text-cream">
    <div class="h-px bg-linear-to-l from-transparent via-gold/50 to-transparent"></div>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="flex flex-col items-center gap-3 text-center">
            <x-logo :size="40" />
            <p class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold" dir="ltr">{{ __('site.name_latin') }}</p>
            <p class="text-gold">{{ __('site.slogan_primary') }}</p>
            <p class="max-w-md text-sm leading-7 text-cream/70">{{ __('site.footer_about') }}</p>
        </div>
        @if ($pages->isNotEmpty())
            <nav class="mt-8" aria-label="{{ __('site.footer_nav') }}">
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm">
                    @foreach ($pages as $page)
                        <li><a href="{{ $page->url() }}" class="text-cream/80 transition-colors hover:text-gold">{{ $page->title }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endif
        <p class="mt-8 border-t border-cream/10 pt-6 text-center text-xs text-cream/50">
            {{ __('site.footer_rights') }}
        </p>
    </div>
</footer>
