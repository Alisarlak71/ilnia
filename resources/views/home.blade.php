{{-- Temporary home page (coming soon). The real home page is built in Phase 1. --}}
<x-layouts.app>
    <section class="mx-auto max-w-3xl px-4 py-16 text-center sm:py-24">
        <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="88" height="88" alt="" class="mx-auto">
        <h1 class="mt-8 text-3xl font-extrabold leading-snug sm:text-4xl">
            {{ __('site.slogan_campaign') }}
        </h1>
        <div class="mx-auto mt-6 h-px w-24 bg-gold-deep/60"></div>
        <h2 class="mt-6 text-lg font-bold">{{ __('site.coming_soon_title') }}</h2>
        <p class="mt-2 text-muted">{{ __('site.coming_soon_text') }}</p>
    </section>
</x-layouts.app>
