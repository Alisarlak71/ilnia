{{-- Home page: hero · three brand values · products · story («به رسم نیاکان»). See docs/DESIGN.md. --}}
<x-layouts.app>
    {{--
        Hero: dark, continuing the header, like the brand's Instagram post (ilnia-post-2).
        The picture is the brand's own illustrated jar (rendered from ilnia-post-2.html, with the logo's
        mountain line behind it), not a photograph. Swap it for a real product photo once one exists.
    --}}
    <section class="relative isolate overflow-hidden bg-ink text-cream" aria-labelledby="hero-title">
        {{-- Warm light behind the jar --}}
        <div class="pointer-events-none absolute -z-10 h-[38rem] w-[38rem] rounded-full bg-[radial-gradient(circle,rgba(200,144,46,0.28),transparent_62%)] max-md:left-1/2 max-md:top-[58%] max-md:-translate-x-1/2 md:left-[4%] md:top-1/2 md:-translate-y-1/2"
             aria-hidden="true"></div>

        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 pb-14 pt-12 sm:pb-20 sm:pt-16 md:grid-cols-2 md:gap-12 lg:min-h-[calc(100svh-4.8rem)] lg:py-16">
            <div class="text-center md:text-start">
                <div class="hero-rise flex items-center justify-center gap-3 md:justify-start">
                    <span class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold" dir="ltr">ILNIA</span>
                    <span class="h-px w-10 bg-gold/40" aria-hidden="true"></span>
                    <span class="text-sm text-cream/70">{{ __('home.hero_eyebrow') }}</span>
                </div>

                <h1 id="hero-title" class="hero-rise hero-rise-1 mt-6 text-[2.5rem] font-black leading-[1.3] sm:text-5xl lg:text-[3.5rem] lg:leading-[1.3] xl:text-[3.75rem]">
                    <span class="block lg:whitespace-nowrap">{{ __('home.hero_title_1') }}</span>
                    <span class="block text-gold lg:whitespace-nowrap">{{ __('home.hero_title_2') }}</span>
                </h1>

                <p class="hero-rise hero-rise-2 mx-auto mt-6 max-w-md text-lg leading-9 text-cream/75 md:mx-0">{{ __('home.hero_text') }}</p>

                <div class="hero-rise hero-rise-3 mt-9 flex flex-col items-center gap-3 sm:flex-row sm:justify-center md:justify-start">
                    <a href="#products"
                       class="group inline-flex w-full items-center justify-center gap-3 rounded-btn border border-gold bg-gold px-8 py-3.5 font-bold text-ink transition hover:-translate-y-px hover:bg-cream hover:border-cream active:translate-y-px sm:w-auto">
                        {{ __('home.hero_cta') }}
                        <span class="transition-transform group-hover:-translate-x-1" aria-hidden="true">←</span>
                    </a>
                    <a href="#story"
                       class="inline-flex w-full items-center justify-center rounded-btn border border-cream/25 px-8 py-3.5 transition hover:-translate-y-px hover:border-gold hover:text-gold active:translate-y-px sm:w-auto">
                        {{ __('home.hero_secondary') }}
                    </a>
                </div>

                @if ($shippingMethod?->free_over_weight_grams)
                    <p class="hero-rise hero-rise-3 mt-8 flex items-center justify-center gap-2 text-sm text-cream/60 md:justify-start">
                        <span class="size-1.5 rounded-full bg-gold" aria-hidden="true"></span>
                        {{ __('home.hero_fact', ['weight' => \App\Support\Weight::kg($shippingMethod->free_over_weight_grams)]) }}
                    </p>
                @endif
            </div>

            <div class="hero-rise hero-rise-2 relative mx-auto w-full max-w-sm md:max-w-md">
                <picture>
                    <source type="image/webp"
                            srcset="{{ asset('images/hero/ilnia-jar-640.webp') }} 640w, {{ asset('images/hero/ilnia-jar-960.webp') }} 960w, {{ asset('images/hero/ilnia-jar-1440.webp') }} 1440w"
                            sizes="(min-width: 768px) 28rem, 90vw">
                    <img src="{{ asset('images/hero/ilnia-jar-960.jpg') }}"
                         srcset="{{ asset('images/hero/ilnia-jar-640.jpg') }} 640w, {{ asset('images/hero/ilnia-jar-960.jpg') }} 960w, {{ asset('images/hero/ilnia-jar-1440.jpg') }} 1440w"
                         sizes="(min-width: 768px) 28rem, 90vw"
                         width="1440" height="1800" fetchpriority="high" decoding="async"
                         alt="{{ __('home.hero_image_alt') }}"
                         class="aspect-[4/5] w-full rounded-card object-cover shadow-[0_40px_80px_-30px_rgba(0,0,0,0.8)] ring-1 ring-gold/15">
                </picture>
            </div>
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

                <div class="mt-10 text-center">
                    <a href="{{ route('products.index') }}"
                       class="inline-block rounded-btn border border-gold-deep px-7 py-3 font-bold transition-colors hover:bg-gold-deep hover:text-cream">
                        {{ __('shop.all_products') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Story --}}
    <section id="story" class="scroll-mt-6 bg-ink text-cream" aria-labelledby="story-title">
        <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:py-20">
            <img src="{{ asset('brand/logo-mark-gold.svg') }}" width="64" height="64" alt="" class="mx-auto">
            <p class="mt-6 text-sm font-bold text-gold">{{ __('home.story_eyebrow') }}</p>
            <h2 id="story-title" class="mt-2 text-2xl font-extrabold sm:text-3xl">{{ __('home.story_title') }}</h2>
            <x-ornament tone="light" class="mt-5" />
            <p class="mt-6 leading-8 text-cream/80">{{ __('home.story_text') }}</p>
        </div>
    </section>
</x-layouts.app>
