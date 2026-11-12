{{--
    Product card for the home page and product list.
    Expects $product with `variants` and `images` loaded.
    The whole card links to the product page.
--}}
@props(['product'])

@php
    $image = $product->mainImage();
    $activeVariants = $product->variants->where('is_active', true);
    $startingPrice = $product->startingPrice();
    $available = $activeVariants->contains(fn ($variant) => $variant->isPurchasable());
@endphp

<article {{ $attributes->merge(['class' => 'group relative flex flex-col']) }}>
    <div class="relative aspect-[4/5] overflow-hidden rounded-card bg-paper ring-1 ring-line/80 transition duration-500 ease-out group-hover:-translate-y-0.5 group-hover:shadow-[0_22px_40px_-24px_rgba(30,26,22,0.45)] group-hover:ring-gold-deep/40 group-focus-within:ring-2 group-focus-within:ring-honey">
        @if ($image)
            <img src="{{ $image->url() }}" alt="{{ $image->altText() }}" loading="lazy"
                 class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.03]">
        @else
            <x-image-placeholder class="absolute inset-0 h-full w-full" />
        @endif

        @if ($startingPrice !== null && ! $available)
            <span class="absolute start-3 top-3 rounded-full bg-ink/85 px-3 py-1 text-xs text-cream backdrop-blur-sm">
                {{ __('shop.out_of_stock') }}
            </span>
        @endif

        {{-- "View" hint slides up on hover (decorative; the whole card is the link). --}}
        <span class="pointer-events-none absolute inset-x-3 bottom-3 translate-y-2 rounded-btn bg-ink/90 py-2 text-center text-xs font-bold text-cream opacity-0 backdrop-blur-sm transition duration-500 group-hover:translate-y-0 group-hover:opacity-100"
              aria-hidden="true">{{ __('shop.view_product') }}</span>
    </div>

    <div class="flex flex-1 flex-col pt-4">
        <h3 class="text-base font-extrabold leading-7 sm:text-lg">
            {{-- The link covers the whole card (after:inset-0). --}}
            <a href="{{ route('products.show', $product) }}" class="outline-none after:absolute after:inset-0">{{ $product->name }}</a>
        </h3>

        @if ($activeVariants->isNotEmpty())
            <p class="mt-0.5 text-xs tracking-wide text-muted">{{ $activeVariants->pluck('title')->join(' · ') }}</p>
        @endif

        <div class="mt-auto flex items-center gap-3 pt-3">
            <span class="h-px w-6 bg-gold-deep/50 transition-all duration-500 group-hover:w-10" aria-hidden="true"></span>
            <p class="text-sm font-bold text-gold-deep">
                @if ($startingPrice !== null)
                    {{ __('shop.from_price', ['price' => \App\Support\Toman::format($startingPrice)]) }}
                @else
                    <span class="font-normal text-muted">{{ __('shop.price_soon') }}</span>
                @endif
            </p>
        </div>
    </div>
</article>
