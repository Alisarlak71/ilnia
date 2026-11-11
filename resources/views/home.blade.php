{{-- Home page: hero · three brand values · products · story («به رسم نیاکان»). See docs/DESIGN.md. --}}
<x-layouts.app>
    {{-- Hero --}}
    <section class="border-b border-line">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:py-16 md:grid-cols-2 md:py-20">
            <div class="text-center md:text-start">
                <p class="text-sm font-bold text-gold-deep">{{ __('home.hero_eyebrow') }}</p>
                <h1 class="mt-3 text-3xl font-black leading-snug sm:text-4xl lg:text-5xl lg:leading-tight">
                    {{ __('home.hero_title') }}
                </h1>
                <p class="mx-auto mt-5 max-w-md text-muted md:mx-0">{{ __('home.hero_text') }}</p>
                <a href="#products"
                   class="mt-8 inline-block rounded-btn border border-ink bg-ink px-7 py-3 font-bold text-cream transition-colors hover:border-gold">
                    {{ __('home.hero_cta') }}
                </a>
            </div>

            {{-- Real product photo goes here once it exists; until then, the brand placeholder. --}}
            <x-image-placeholder class="mx-auto aspect-[4/5] w-full max-w-sm rounded-card border border-line md:max-w-none" />
        </div>
    </section>

    {{-- Three brand values --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:py-20" aria-labelledby="values-title">
        <h2 id="values-title" class="text-center text-2xl font-extrabold">{{ __('home.values_title') }}</h2>
        <x-ornament class="mt-4" />

        <div class="mt-10 grid gap-5 sm:grid-cols-3">
            @foreach (__('home.values') as $i => $value)
                <div class="rounded-card border border-line bg-paper p-6 text-center">
                    <span class="font-bold text-gold-deep">{{ \App\Support\Digits::toPersian($i + 1) }}</span>
                    <h3 class="mt-2 text-xl font-extrabold">{{ $value['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ $value['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Products --}}
    <section id="products" class="scroll-mt-6 border-y border-line bg-paper/60" aria-labelledby="products-title">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:py-20">
            <h2 id="products-title" class="text-center text-2xl font-extrabold">{{ __('home.products_title') }}</h2>
            <x-ornament class="mt-4" />

            @if ($products->isEmpty())
                <p class="mt-10 text-center text-muted">{{ __('home.products_empty') }}</p>
            @else
                <div class="mt-10 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Story --}}
    <section class="bg-ink text-cream" aria-labelledby="story-title">
        <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:py-20">
            <img src="{{ asset('brand/logo-mark-gold.svg') }}" width="64" height="64" alt="" class="mx-auto">
            <p class="mt-6 text-sm font-bold text-gold">{{ __('home.story_eyebrow') }}</p>
            <h2 id="story-title" class="mt-2 text-2xl font-extrabold sm:text-3xl">{{ __('home.story_title') }}</h2>
            <x-ornament tone="light" class="mt-5" />
            <p class="mt-6 leading-8 text-cream/80">{{ __('home.story_text') }}</p>
        </div>
    </section>
</x-layouts.app>
