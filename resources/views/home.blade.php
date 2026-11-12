{{-- Home page: hero · three brand values · products · story («به رسم نیاکان»). See docs/DESIGN.md. --}}
<x-layouts.app>
    {{--
        Hero: one full-bleed photograph (a bee on honeycomb, supplied by the owner), mirrored so the bee
        sits on the left and the calm, blurred greenery on the right holds the Persian text.
        A dark gradient from the right keeps the words readable; on phones it darkens from the foot.
    --}}
    <section class="relative isolate flex min-h-[calc(100svh-4.8rem)] items-end overflow-hidden bg-ink text-cream md:items-center" aria-labelledby="hero-title">
        <picture>
            <source type="image/webp"
                    srcset="{{ asset('images/hero/bee-comb-768.webp') }} 768w, {{ asset('images/hero/bee-comb-1280.webp') }} 1280w, {{ asset('images/hero/bee-comb-1672.webp') }} 1672w"
                    sizes="100vw">
            <img src="{{ asset('images/hero/bee-comb-1280.jpg') }}"
                 srcset="{{ asset('images/hero/bee-comb-768.jpg') }} 768w, {{ asset('images/hero/bee-comb-1280.jpg') }} 1280w, {{ asset('images/hero/bee-comb-1672.jpg') }} 1672w"
                 sizes="100vw" width="1672" height="941" fetchpriority="high" decoding="async"
                 alt="{{ __('home.hero_image_alt') }}"
                 class="hero-photo absolute inset-0 -z-20 h-full w-full object-cover object-[28%_50%] md:object-[8%_50%] xl:object-[28%_50%]">
        </picture>

        {{-- Readability: dark from the right (text side) on wide screens, from the foot on phones --}}
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink via-ink/70 to-ink/10 md:bg-gradient-to-l md:from-ink/95 md:via-ink/70 md:to-transparent xl:from-ink/90 xl:via-ink/55" aria-hidden="true"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-24 bg-gradient-to-t from-ink/60 to-transparent" aria-hidden="true"></div>

        <div class="mx-auto w-full max-w-6xl px-4 pb-12 pt-[40svh] md:py-20">
            <div class="max-w-xl text-center md:text-start">
                <div class="hero-rise flex items-center justify-center gap-3 md:justify-start">
                    <span class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold" dir="ltr">ILNIA</span>
                    <span class="h-px w-10 bg-gold/50" aria-hidden="true"></span>
                    <span class="text-sm text-cream/80">{{ __('home.hero_eyebrow') }}</span>
                </div>

                <h1 id="hero-title" class="hero-rise hero-rise-1 mt-6 text-[2.5rem] font-black leading-[1.3] drop-shadow-[0_2px_18px_rgba(0,0,0,0.45)] sm:text-5xl lg:text-[3.75rem] lg:leading-[1.25]">
                    <span class="block text-gold lg:whitespace-nowrap">{{ __('home.hero_title_1') }}</span>
                    <span class="block lg:whitespace-nowrap">{{ __('home.hero_title_2') }}</span>
                </h1>

                <p class="hero-rise hero-rise-2 mx-auto mt-6 max-w-md text-lg leading-9 text-cream/85 md:mx-0">{{ __('home.hero_text') }}</p>

                <div class="hero-rise hero-rise-3 mt-9 flex flex-col items-center gap-3 sm:flex-row sm:justify-center md:justify-start">
                    <a href="#products"
                       class="group inline-flex w-full items-center justify-center gap-3 rounded-btn border border-gold bg-gold px-8 py-3.5 font-bold text-ink shadow-[0_10px_30px_-10px_rgba(226,180,90,0.6)] transition hover:-translate-y-px hover:border-cream hover:bg-cream active:translate-y-px sm:w-auto">
                        {{ __('home.hero_cta') }}
                        <span class="transition-transform group-hover:-translate-x-1" aria-hidden="true">←</span>
                    </a>
                    <a href="#story"
                       class="inline-flex w-full items-center justify-center rounded-btn border border-cream/35 bg-ink/20 px-8 py-3.5 backdrop-blur-sm transition hover:-translate-y-px hover:border-gold hover:text-gold active:translate-y-px sm:w-auto">
                        {{ __('home.hero_secondary') }}
                    </a>
                </div>

                @if ($shippingMethod?->free_over_weight_grams)
                    <p class="hero-rise hero-rise-3 mt-8 flex items-center justify-center gap-2 text-sm text-cream/75 md:justify-start">
                        <span class="size-1.5 rounded-full bg-gold" aria-hidden="true"></span>
                        {{ __('home.hero_fact', ['weight' => \App\Support\Weight::kg($shippingMethod->free_over_weight_grams)]) }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Three brand values --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:py-20" aria-labelledby="values-title">
        <h2 id="values-title" class="text-center text-2xl font-extrabold">{{ __('home.values_title') }}</h2>
        <x-ornament class="mt-4" />

        <div class="mt-12 grid gap-6 sm:grid-cols-3">
            @foreach (__('home.values') as $i => $value)
                {{-- Card: soft shadow at rest; on hover it lifts, a gold line draws across the top,
                     the icon fills with gold and the faint number warms up. --}}
                <div class="value-card group relative isolate overflow-hidden rounded-card border border-line bg-paper px-6 pb-8 pt-9 text-center shadow-[0_1px_2px_rgba(30,26,22,0.05),0_8px_24px_-16px_rgba(30,26,22,0.25)] transition duration-500 ease-out hover:-translate-y-1.5 hover:border-gold-deep/40 hover:shadow-[0_2px_4px_rgba(30,26,22,0.05),0_28px_50px_-24px_rgba(168,115,27,0.45)] active:translate-y-0 active:scale-[.99] motion-reduce:transform-none motion-reduce:transition-none">
                    {{-- Gold line drawn from the centre on hover --}}
                    <span class="absolute inset-x-0 top-0 mx-auto h-0.5 w-0 bg-gradient-to-l from-transparent via-gold-deep to-transparent transition-all duration-700 ease-out group-hover:w-full" aria-hidden="true"></span>

                    {{-- Large faint number in the corner --}}
                    <span class="pointer-events-none absolute -bottom-6 end-3 -z-10 select-none text-[7rem] font-black leading-none text-gold-deep/[0.06] transition duration-700 group-hover:-translate-y-2 group-hover:text-gold-deep/[0.14]" aria-hidden="true">{{ \App\Support\Digits::toPersian($i + 1) }}</span>

                    {{-- Icon badge --}}
                    <span class="mx-auto flex size-14 items-center justify-center rounded-full border border-gold-deep/30 bg-cream text-gold-deep transition duration-500 group-hover:rotate-[8deg] group-hover:scale-110 group-hover:border-gold-deep group-hover:bg-gold-deep group-hover:text-cream" aria-hidden="true">
                        @switch($i)
                            @case(0)
                                {{-- Authenticity: a honey drop (from the logo) --}}
                                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3.5S6.5 10 6.5 14a5.5 5.5 0 0 0 11 0C17.5 10 12 3.5 12 3.5z"/><path d="M9.6 14.6a2.6 2.6 0 0 0 2.4 2.4" stroke-linecap="round"/></svg>
                                @break
                            @case(1)
                                {{-- Honesty: a seal with a check --}}
                                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round"><path d="M12 3l2.2 1.6 2.7-.1.8 2.6 2.2 1.6-.9 2.6.9 2.6-2.2 1.6-.8 2.6-2.7-.1L12 21l-2.2-1.6-2.7.1-.8-2.6-2.2-1.6.9-2.6-.9-2.6 2.2-1.6.8-2.6 2.7.1z"/><path d="M8.8 12.2l2.2 2.2 4.2-4.4"/></svg>
                                @break
                            @default
                                {{-- Luxury: a gem --}}
                                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" stroke-linecap="round"><path d="M6.5 4.5h11l3 5-8.5 10-8.5-10z"/><path d="M3.5 9.5h17M9.5 4.5 8 9.5l4 10 4-10-1.5-5"/></svg>
                        @endswitch
                    </span>

                    <h3 class="mt-5 text-xl font-extrabold transition-colors duration-500 group-hover:text-gold-deep">{{ $value['title'] }}</h3>
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
