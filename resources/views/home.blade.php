{{--
    Home page, built with taste-skill 2.5 (kind: product, site-kind other).
    Deal: hero photo (full-bleed photograph, words in a solid box on the calm side) · moment
    pinned-chapters · nav split (site header) · footer columns · close photo · rhythm dark-band ·
    buttons arrow. Palette and type: the brand's own (brand/tokens.css, Vazirmatn); flat grounds.

    Photographs: licensed Wikimedia Commons STAND-INS of the setting (nomad camp, black tent, Zagros
    valley, a bee on a flower, a flock), approved by the owner; no jar or honey that could pass for our
    product (honesty rule). Credits: one footer line. Replace with the owner's own photographs.
--}}
@php
    use App\Support\Weight;

    $img = fn (string $name) => asset('images/landing/'.$name);
@endphp

<x-layouts.app>
    <x-slot:head>
        <link rel="preload" as="image" href="{{ $img('hero-camp-1920.jpg') }}" imagesrcset="{{ $img('hero-camp-960.jpg') }} 960w, {{ $img('hero-camp-1280.jpg') }} 1280w, {{ $img('hero-camp-1920.jpg') }} 1920w" imagesizes="100vw">
        <link rel="modulepreload" href="{{ asset('assets/moments/moments.js') }}">
        <link rel="modulepreload" href="{{ asset('assets/moments/lib.js') }}">
        <script>document.documentElement.classList.add('mo-js');setTimeout(()=>document.documentElement.classList.add('mo-safe'),2500)</script>
        <script type="module" src="{{ asset('assets/moments/moments.js') }}"></script>
    </x-slot:head>

    {{-- ============================== Hero: one screen ============================== --}}
    <section class="relative flex h-[calc(100svh-4.8rem)] min-h-[34rem] flex-col overflow-hidden bg-cream lg:block" aria-labelledby="hero-title">
        <img src="{{ $img('hero-camp-1920.jpg') }}"
             srcset="{{ $img('hero-camp-960.jpg') }} 960w, {{ $img('hero-camp-1280.jpg') }} 1280w, {{ $img('hero-camp-1920.jpg') }} 1920w"
             sizes="100vw" width="1920" height="1081" fetchpriority="high" decoding="async"
             alt="{{ __('home.hero_alt') }}"
             class="h-[36svh] w-full shrink-0 object-cover object-[72%_50%] sm:h-[50svh] lg:absolute lg:inset-0 lg:h-full">

        <div class="relative mx-auto flex w-full max-w-6xl flex-1 px-4 lg:h-full lg:items-center">
            <div class="w-full py-6 lg:ms-auto lg:max-w-[34rem] lg:rounded-card lg:bg-cream lg:p-12">
                @if ($shippingMethod?->free_over_weight_grams)
                    <p class="rise text-sm text-gold-deep">
                        {{ __('home.hero_fact', ['weight' => Weight::kg($shippingMethod->free_over_weight_grams)]) }}
                    </p>
                @endif
                <h1 id="hero-title" class="rise rise-1 mt-3 text-[2.15rem] font-extrabold leading-[1.35] sm:text-5xl sm:leading-[1.3] lg:text-[3.35rem]">
                    <span class="block">{{ __('home.hero_title_1') }}</span>
                    <span class="block">{{ __('home.hero_title_2') }}</span>
                </h1>
                <p class="rise rise-2 mt-4 max-w-md leading-8 text-muted sm:text-lg sm:leading-9 lg:mt-6">{{ __('home.hero_text') }}</p>
                <div class="rise rise-3 mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-6 lg:mt-9">
                    <a href="#products"
                       class="btn-step inline-flex items-center justify-center gap-3 rounded-btn border border-ink bg-ink px-7 py-3.5 font-bold text-cream hover:border-gold">
                        {{ __('home.hero_cta') }} <span aria-hidden="true">←</span>
                    </a>
                    <a href="#story" class="link-draw self-center py-2 text-sm font-bold">{{ __('home.hero_secondary') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== Products: one screen ============================== --}}
    <section id="products" class="flex min-h-svh flex-col bg-paper" aria-labelledby="products-title">
        <div class="mx-auto w-full max-w-6xl px-4 pb-16 pt-20 sm:pt-28">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 id="products-title" class="text-3xl font-extrabold sm:text-[2.6rem] sm:leading-tight">{{ __('home.products_title') }}</h2>
                    <p class="mt-3 max-w-lg leading-8 text-muted">{{ __('home.products_text') }}</p>
                </div>
                @if ($products->isNotEmpty())
                    <a href="{{ route('products.index') }}" class="link-draw self-start py-2 text-sm font-bold sm:self-auto">
                        {{ __('shop.all_products') }} <span aria-hidden="true">←</span>
                    </a>
                @endif
            </div>

            @if ($products->isEmpty())
                <div class="mt-12 rounded-card bg-cream p-10 text-center">
                    <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="48" height="48" alt="" class="mx-auto opacity-30">
                    <p class="mt-4 text-muted">{{ __('home.products_empty') }}</p>
                </div>
            @else
                <div class="mt-12 grid grid-cols-2 gap-x-4 gap-y-10 sm:grid-cols-4 sm:gap-x-6 lg:gap-x-8">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============================== Values: the dealt moment (pinned-chapters), two screens live ============================== --}}
    <section id="values" class="moment-tokens relative bg-cream" data-moment="pinned-chapters" aria-labelledby="values-title">
        <div class="mo-pc__grid mx-auto w-full max-w-6xl px-4 py-20 sm:py-28">
            <div class="mo-pc__text">
                <h2 id="values-title" class="text-3xl font-extrabold sm:text-[2.6rem] sm:leading-tight">{{ __('home.values_title') }}</h2>
                <ol class="mo-pc__steps">
                    @foreach (__('home.values') as $value)
                        <li class="mo-pc__step">
                            <h3>{{ $value['title'] }}</h3>
                            <p>{{ $value['text'] }}</p>
                            <figure class="mo-pc__visual">
                                <img src="{{ $img($value['image'].'-1280.jpg') }}"
                                     srcset="{{ $img($value['image'].'-960.jpg') }} 960w, {{ $img($value['image'].'-1280.jpg') }} 1280w"
                                     sizes="(max-width: 1023px) 100vw, 50vw" width="1280" height="853" loading="lazy" decoding="async"
                                     alt="{{ $value['alt'] }}" style="object-position: {{ $value['position'] }}">
                            </figure>
                        </li>
                    @endforeach
                </ol>
            </div>
            <div class="mo-pc__visuals overflow-hidden rounded-card"></div>
        </div>
    </section>

    {{-- ============================== Story: the dark band, one screen ============================== --}}
    <section id="story" class="flex min-h-svh items-center bg-ink text-cream" aria-labelledby="story-title">
        <div class="mx-auto grid w-full max-w-6xl items-center gap-12 px-4 py-20 sm:py-28 lg:grid-cols-2 lg:gap-16">
            <div class="min-w-0">
                <h2 id="story-title" class="text-3xl font-extrabold sm:text-[2.6rem] sm:leading-tight">{{ __('home.story_title') }}</h2>

                {{-- The name, read right to left: ایل + نیا = ایلنیا --}}
                <p class="mt-10 flex flex-wrap items-baseline gap-x-4 gap-y-2 text-4xl font-extrabold sm:text-6xl" aria-label="{{ __('home.story_equation_label') }}">
                    <span>ایل</span><span class="text-2xl font-normal text-gold/70 sm:text-4xl" aria-hidden="true">+</span>
                    <span>نیا</span><span class="text-2xl font-normal text-gold/70 sm:text-4xl" aria-hidden="true">=</span>
                    <span class="font-brand text-gold">{{ __('site.name') }}</span>
                </p>

                <p class="mt-8 max-w-lg leading-9 text-cream/75">{{ __('home.story_text') }}</p>
                @if ($aboutPage)
                    <a href="{{ $aboutPage->url() }}" class="link-draw mt-8 inline-block py-2 text-sm font-bold text-gold">
                        {{ __('home.story_more') }} <span aria-hidden="true">←</span>
                    </a>
                @endif
            </div>

            <figure class="group overflow-hidden rounded-card">
                <img src="{{ $img('flock-1280.jpg') }}"
                     srcset="{{ $img('flock-960.jpg') }} 960w, {{ $img('flock-1280.jpg') }} 1280w"
                     sizes="(max-width: 1023px) 100vw, 50vw" width="1280" height="719" loading="lazy" decoding="async"
                     alt="{{ __('home.story_alt') }}"
                     class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-[1.03] lg:aspect-[4/5]">
            </figure>
        </div>
    </section>

    {{-- The close (photo) opens the footer: one screen with the footer's columns. --}}
    @push('footer-closing')
        <div class="relative isolate overflow-hidden">
            <img src="{{ $img('ridge-1280.jpg') }}" srcset="{{ $img('ridge-960.jpg') }} 960w, {{ $img('ridge-1280.jpg') }} 1280w" sizes="100vw" width="1280" height="853" loading="lazy" decoding="async" alt="{{ __('home.closing_alt') }}"
                 class="absolute inset-0 -z-10 h-full w-full object-cover">
            <div class="mx-auto flex max-w-6xl px-4 py-16 sm:py-20">
                <div class="w-full rounded-card bg-ink px-8 py-10 sm:w-auto sm:min-w-[26rem] sm:px-12">
                    <p class="text-3xl font-extrabold text-cream sm:text-4xl">{{ __('site.slogan_primary') }}</p>
                    <a href="{{ route('products.index') }}"
                       class="btn-step mt-7 inline-flex items-center gap-3 rounded-btn border border-gold bg-gold px-7 py-3.5 font-bold text-ink hover:bg-cream">
                        {{ __('home.closing_cta') }} <span aria-hidden="true">←</span>
                    </a>
                </div>
            </div>
        </div>
    @endpush

    @push('footer-credits')
        {{ __('home.photo_credits') }}
    @endpush
</x-layouts.app>
