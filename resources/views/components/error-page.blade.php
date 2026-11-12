{{--
    Friendly error page inside the normal site layout (404, 403, 419, 429).
    Server errors (500, 503) use errors/minimal.blade.php instead: no database, no Livewire.
--}}
@props(['code', 'title', 'text'])

<x-layouts.app :title="$title" :noindex="true">
    <section class="mx-auto max-w-xl px-4 py-16 text-center sm:py-24">
        <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="64" height="64" alt="" class="mx-auto opacity-30">
        <p class="mt-6 font-extrabold leading-none tracking-wider text-gold-deep text-7xl" aria-hidden="true">{{ \App\Support\Digits::toPersian($code) }}</p>
        <h1 class="mt-6 text-2xl font-extrabold">{{ $title }}</h1>
        <x-ornament class="mt-4" />
        <p class="mx-auto mt-5 max-w-md leading-8 text-muted">{{ $text }}</p>

        <div class="mx-auto mt-8 flex max-w-xs flex-col justify-center gap-3 sm:max-w-none sm:flex-row">
            {{ $slot }}
            @if ($slot->isEmpty())
                <a href="{{ route('home') }}"
                   class="rounded-btn border border-ink bg-ink px-7 py-3 font-bold text-cream transition-colors hover:border-gold">{{ __('errors.home') }}</a>
                <a href="{{ route('products.index') }}"
                   class="rounded-btn border border-gold-deep px-7 py-3 hover:bg-paper">{{ __('errors.products') }}</a>
            @endif
        </div>
    </section>
</x-layouts.app>
