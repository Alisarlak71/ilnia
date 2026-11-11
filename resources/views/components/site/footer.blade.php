{{-- Dark site footer. Links to about/contact/terms pages are added in Phase 4. --}}
<footer class="mt-16 bg-ink text-cream">
    <div class="h-px bg-gradient-to-l from-transparent via-gold/50 to-transparent"></div>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="flex flex-col items-center gap-3 text-center">
            <x-logo :size="40" />
            <p class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold" dir="ltr">{{ __('site.name_latin') }}</p>
            <p class="text-gold">{{ __('site.slogan_primary') }}</p>
            <p class="max-w-md text-sm leading-7 text-cream/70">{{ __('site.footer_about') }}</p>
        </div>
        <p class="mt-8 border-t border-cream/10 pt-6 text-center text-xs text-cream/50">
            {{ __('site.footer_rights') }}
        </p>
    </div>
</footer>
