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

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-card border border-line bg-paper transition-colors hover:border-gold-deep focus-within:ring-2 focus-within:ring-honey']) }}>
    <div class="relative aspect-square overflow-hidden">
        @if ($image)
            <img src="{{ $image->url() }}" alt="{{ $image->altText() }}" loading="lazy"
                 class="absolute inset-0 h-full w-full object-cover">
        @else
            <x-image-placeholder class="h-full w-full" />
        @endif

        @if ($startingPrice !== null && ! $available)
            <span class="absolute start-3 top-3 rounded-btn bg-ink/85 px-3 py-1 text-xs text-cream">
                {{ __('shop.out_of_stock') }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-1 p-4">
        <h3 class="text-base font-bold leading-7">
            {{-- The link covers the whole card (after:inset-0). --}}
            <a href="{{ route('products.show', $product) }}" class="outline-none after:absolute after:inset-0">{{ $product->name }}</a>
        </h3>

        @if ($activeVariants->isNotEmpty())
            <p class="text-sm text-muted">{{ $activeVariants->pluck('title')->join('، ') }}</p>
        @endif

        <p class="mt-auto pt-2 font-bold text-gold-deep">
            @if ($startingPrice !== null)
                {{ __('shop.from_price', ['price' => \App\Support\Toman::format($startingPrice)]) }}
            @else
                <span class="font-normal text-muted">{{ __('shop.price_soon') }}</span>
            @endif
        </p>
    </div>
</article>
