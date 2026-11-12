{{--
    Home page, minimal-luxury layout: hero (typography + brand arch) · three values (editorial list)
    · products · name story («ایل + نیا») · closing line. See docs/DESIGN.md.
    Honesty rule: no product photo in the hero until a real one exists (no stock photos), no claims.
--}}
@php
    use App\Support\Digits;

    $aboutPage = \App\Models\Page::published()->where('slug', 'about')->first();
@endphp

<x-layouts.app>
    {{-- ============================== Hero ============================== --}}
    <section class="bg-grain relative overflow-hidden">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 pb-16 pt-12 sm:pb-24 sm:pt-16 lg:grid-cols-12 lg:gap-8 lg:pb-28 lg:pt-20">
            {{-- Text --}}
            <div class="text-center lg:col-span-7 lg:text-start">
                <div class="rise flex items-center justify-center gap-3 lg:justify-start">
                    <span class="font-latin ps-[0.55em] text-xs tracking-[0.55em] text-gold-deep" dir="ltr">ILNIA</span>
                    <span class="h-px w-10 bg-gold-deep/40" aria-hidden="true"></span>
                    <span class="text-sm text-muted">{{ __('home.hero_eyebrow') }}</span>
                </div>

                <h1 class="rise rise-1 mt-7 text-[2.4rem] font-black leading-[1.35] tracking-tight sm:text-6xl sm:leading-[1.3] lg:text-[3.4rem] xl:text-[3.9rem]">
                    <span class="block sm:whitespace-nowrap">{{ __('home.hero_title_1') }}</span>
                    <span class="block text-gold-deep sm:whitespace-nowrap">{{ __('home.hero_title_2') }}</span>
                </h1>

                <p class="rise rise-2 mx-auto mt-7 max-w-md text-lg leading-9 text-muted lg:mx-0">{{ __('home.hero_text') }}</p>

                <div class="rise rise-3 mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row lg:justify-start">
                    <a href="#products"
                       class="group inline-flex items-center gap-3 rounded-btn border border-ink bg-ink px-8 py-3.5 font-bold text-cream transition-colors duration-300 hover:border-gold">
                        {{ __('home.hero_cta') }}
                        <span class="transition-transform duration-300 group-hover:-translate-x-1" aria-hidden="true">←</span>
                    </a>
                    <a href="#story" class="border-b border-gold-deep/40 pb-0.5 text-sm transition-colors hover:border-gold-deep hover:text-gold-deep">
                        {{ __('home.hero_secondary') }}
                    </a>
                </div>
            </div>

            {{-- Brand arch: the black tent's doorway, with the mark and a soft honey glow. --}}
            <div class="rise rise-2 lg:col-span-5">
                <div class="relative mx-auto aspect-[4/5] w-full max-w-[22rem] overflow-hidden rounded-t-full rounded-b-card bg-ink shadow-[0_40px_80px_-40px_rgba(30,26,22,0.6)] lg:max-w-none">
                    <div class="bg-gilim absolute inset-0 text-gold/[0.06]" aria-hidden="true"></div>
                    <div class="honey-glow absolute left-1/2 top-[42%] size-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle,rgba(226,180,90,0.38),transparent_65%)]" aria-hidden="true"></div>

                    {{-- Thin double frame following the arch --}}
                    <div class="absolute inset-4 rounded-t-full rounded-b-[10px] border border-gold/25" aria-hidden="true"></div>
                    <div class="absolute inset-6 rounded-t-full rounded-b-[8px] border border-gold/10" aria-hidden="true"></div>

                    <div class="relative flex h-full flex-col items-center justify-center px-8 text-center">
                        <img src="{{ asset('brand/logo-mark-gold.svg') }}" width="132" height="132" alt="" class="w-28 sm:w-32">
                        <p class="mt-6 font-brand text-4xl leading-none text-cream">{{ __('site.name') }}</p>
                        <p class="font-latin mt-3 ps-[0.55em] text-[0.7rem] tracking-[0.55em] text-gold" dir="ltr">ILNIA</p>
                        <x-ornament tone="light" class="mt-6 opacity-80" />
                        <p class="mt-5 text-gold">{{ __('site.slogan_primary') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== Values ============================== --}}
    <section class="border-y border-line bg-paper" aria-labelledby="values-title">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-20 sm:py-28 lg:grid-cols-12 lg:gap-8">
            <div class="reveal lg:col-span-4">
                <div class="lg:sticky lg:top-10">
                    <p class="text-sm font-bold text-gold-deep">{{ __('home.values_eyebrow') }}</p>
                    <h2 id="values-title" class="mt-3 text-3xl font-black leading-snug sm:text-4xl">{{ __('home.values_title') }}</h2>
                    <p class="mt-5 max-w-xs leading-8 text-muted">{{ __('home.values_text') }}</p>
                </div>
            </div>

            <ol class="lg:col-span-7 lg:col-start-6">
                @foreach (__('home.values') as $i => $value)
                    <li class="reveal group grid grid-cols-[auto_1fr] gap-x-6 border-t border-line py-9 first:border-t-0 first:pt-0 sm:gap-x-10 sm:py-11">
                        <span class="text-5xl font-black leading-none text-gold-deep/25 transition-colors duration-500 group-hover:text-gold-deep sm:text-6xl" aria-hidden="true">
                            {{ Digits::toPersian($i + 1) }}
                        </span>
                        <div>
                            <h3 class="text-2xl font-extrabold sm:text-3xl">{{ $value['title'] }}</h3>
                            <p class="mt-3 max-w-md leading-8 text-muted">{{ $value['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ============================== Products ============================== --}}
    <section id="products" class="scroll-mt-6" aria-labelledby="products-title">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:py-28">
            <div class="reveal flex flex-col items-center gap-6 text-center sm:flex-row sm:items-end sm:justify-between sm:text-start">
                <div>
                    <p class="text-sm font-bold text-gold-deep">{{ __('home.products_eyebrow') }}</p>
                    <h2 id="products-title" class="mt-3 text-3xl font-black sm:text-4xl">{{ __('home.products_title') }}</h2>
                </div>
                @if ($products->isNotEmpty())
                    <a href="{{ route('products.index') }}"
                       class="group inline-flex items-center gap-2 border-b border-gold-deep/40 pb-0.5 text-sm font-bold transition-colors hover:border-gold-deep hover:text-gold-deep">
                        {{ __('shop.all_products') }}
                        <span class="transition-transform duration-300 group-hover:-translate-x-1" aria-hidden="true">←</span>
                    </a>
                @endif
            </div>

            @if ($products->isEmpty())
                <div class="reveal mx-auto mt-14 max-w-md rounded-card border border-line bg-paper p-10 text-center">
                    <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="48" height="48" alt="" class="mx-auto opacity-30">
                    <p class="mt-4 text-muted">{{ __('home.products_empty') }}</p>
                </div>
            @else
                <div class="mt-14 grid grid-cols-2 gap-x-4 gap-y-12 sm:gap-x-8 lg:grid-cols-4">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" class="reveal" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============================== Story: ایل + نیا ============================== --}}
    <section id="story" class="relative scroll-mt-6 overflow-hidden bg-ink text-cream" aria-labelledby="story-title">
        <div class="bg-gilim absolute inset-0 text-gold/[0.04]" aria-hidden="true"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-14 px-4 py-20 sm:py-28 lg:grid-cols-2 lg:gap-16">
            {{-- The name, as an equation --}}
            <div class="reveal flex min-w-0 items-end justify-center gap-3 sm:gap-8 lg:justify-start" aria-hidden="true">
                <div class="text-center">
                    <p class="text-4xl font-black leading-none sm:text-6xl lg:text-7xl">ایل</p>
                    <p class="mt-3 whitespace-nowrap text-[0.7rem] text-cream/60 sm:mt-4 sm:text-xs">{{ __('home.story_il') }}</p>
                </div>
                <p class="pb-8 text-xl font-light text-gold/70 sm:pb-9 sm:text-3xl">+</p>
                <div class="text-center">
                    <p class="text-4xl font-black leading-none sm:text-6xl lg:text-7xl">نیا</p>
                    <p class="mt-3 whitespace-nowrap text-[0.7rem] text-cream/60 sm:mt-4 sm:text-xs">{{ __('home.story_nia') }}</p>
                </div>
                <p class="pb-8 text-xl font-light text-gold/70 sm:pb-9 sm:text-3xl">=</p>
                <div class="text-center">
                    <p class="font-brand text-4xl leading-none text-gold sm:text-6xl lg:text-7xl">{{ __('site.name') }}</p>
                    <p class="mt-3 whitespace-nowrap text-[0.7rem] text-gold/80 sm:mt-4 sm:text-xs">{{ __('site.slogan_primary') }}</p>
                </div>
            </div>

            <div class="reveal min-w-0 text-center lg:text-start">
                <p class="text-sm font-bold text-gold">{{ __('home.story_eyebrow') }}</p>
                <h2 id="story-title" class="mt-3 text-3xl font-black leading-snug sm:text-4xl">{{ __('home.story_title') }}</h2>
                <p class="mx-auto mt-6 max-w-lg leading-9 text-cream/75 lg:mx-0">{{ __('home.story_text') }}</p>
                @if ($aboutPage)
                    <a href="{{ $aboutPage->url() }}" class="mt-8 inline-flex items-center gap-2 border-b border-gold/40 pb-0.5 text-sm text-gold transition-colors hover:border-gold">
                        {{ __('home.story_more') }} <span aria-hidden="true">←</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ============================== Closing line ============================== --}}
    <section class="bg-grain border-b border-line">
        <div class="reveal mx-auto max-w-3xl px-4 py-20 text-center sm:py-24">
            <x-ornament />
            <p class="mt-8 text-3xl font-black leading-snug sm:text-5xl sm:leading-tight">{{ __('home.closing_title') }}</p>
            <p class="mx-auto mt-5 max-w-md leading-8 text-muted">{{ __('home.closing_text') }}</p>
            <a href="{{ route('products.index') }}"
               class="mt-9 inline-block rounded-btn border border-ink bg-ink px-8 py-3.5 font-bold text-cream transition-colors duration-300 hover:border-gold">
                {{ __('home.hero_cta') }}
            </a>
        </div>
    </section>
</x-layouts.app>
