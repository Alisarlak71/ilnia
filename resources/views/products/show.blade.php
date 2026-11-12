{{-- Product page: gallery · weight picker & price · description · story · storage. See docs/DESIGN.md. --}}
@php
    $mainImage = $product->mainImage();
    $offers = $product->variants->filter(fn ($v) => $v->is_active && $v->price !== null);

    // Structured data for Google (real data only: no ratings or reviews).
    // schema.org needs an ISO currency, so prices are given in Rial (Toman × 10).
    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $product->short_description ?: null,
        'image' => $mainImage?->url(),
        'brand' => ['@type' => 'Brand', 'name' => __('site.name')],
        'category' => $product->category->name,
        'offers' => $offers->map(fn ($v) => [
            '@type' => 'Offer',
            'name' => $v->title,
            'price' => $v->price * 10,
            'priceCurrency' => 'IRR',
            'availability' => $v->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('products.show', $product),
        ])->values()->all() ?: null,
    ]);
@endphp
<x-layouts.app :title="$product->name"
               :description="$product->short_description ?: __('shop.meta_product', ['name' => $product->name])"
               :image="$mainImage?->url()"
               :image-alt="$mainImage?->altText()"
               type="product"
               :canonical="route('products.show', $product)">
    <x-slot:head>
        <script type="application/ld+json">@json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    </x-slot:head>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
        {{-- Breadcrumb --}}
        <nav aria-label="{{ __('shop.breadcrumb') }}" class="text-sm text-muted">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-gold-deep">{{ __('site.nav_home') }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('products.index') }}" class="hover:text-gold-deep">{{ __('shop.products_title') }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-gold-deep">{{ $product->category->name }}</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="text-ink">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="mt-6 grid gap-8 md:grid-cols-2 md:gap-12">
            {{-- Gallery --}}
            <div>
                @if ($product->images->isEmpty())
                    <x-image-placeholder class="aspect-square w-full rounded-card border border-line" />
                @else
                    <div x-data="{ active: 0 }">
                        <div class="relative aspect-square overflow-hidden rounded-card border border-line bg-paper">
                            @foreach ($product->images as $i => $image)
                                <img src="{{ $image->url() }}" alt="{{ $image->altText() }}"
                                     @if ($i > 0) loading="lazy" @endif
                                     x-show="active === {{ $i }}"
                                     @if ($i > 0) style="display: none" @endif
                                     class="absolute inset-0 h-full w-full object-cover">
                            @endforeach
                        </div>

                        @if ($product->images->count() > 1)
                            <div class="mt-3 grid grid-cols-5 gap-2">
                                @foreach ($product->images as $i => $image)
                                    <button type="button" x-on:click="active = {{ $i }}"
                                            x-bind:class="active === {{ $i }} ? 'border-gold-deep' : 'border-line'"
                                            x-bind:aria-pressed="(active === {{ $i }}).toString()"
                                            class="aspect-square overflow-hidden rounded-btn border-2 {{ $i === 0 ? 'border-gold-deep' : 'border-line' }}"
                                            aria-label="{{ __('shop.gallery_thumb', ['number' => \App\Support\Digits::toPersian($i + 1)]) }}">
                                        <img src="{{ $image->url() }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Name, short description, weight & price --}}
            <div>
                <p class="text-sm font-bold text-gold-deep">{{ $product->category->name }}</p>
                <h1 class="mt-2 text-3xl font-extrabold leading-snug">{{ $product->name }}</h1>
                @if (filled($product->short_description))
                    <p class="mt-3 text-muted">{{ $product->short_description }}</p>
                @endif

                <div class="mt-6">
                    <livewire:product-purchase :product="$product" />
                </div>
            </div>
        </div>

        {{-- Description, story, storage --}}
        <div class="mx-auto mt-14 max-w-3xl space-y-10">
            @foreach ([
                'description' => $product->description,
                'story' => $product->story,
                'storage' => $product->storage_instructions,
            ] as $key => $text)
                @if (filled($text))
                    <section aria-labelledby="section-{{ $key }}">
                        <h2 id="section-{{ $key }}" class="text-xl font-extrabold">{{ __('shop.'.$key) }}</h2>
                        <div class="mt-2 h-px w-16 bg-gold-deep/60"></div>
                        <p class="mt-4 leading-8 text-ink/85">{!! nl2br(e($text)) !!}</p>
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</x-layouts.app>
