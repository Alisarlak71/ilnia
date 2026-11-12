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

{{-- Same language as the home values cards: soft shadow at rest; on hover the card lifts with a warm
     shadow, a gold line draws across the top, the photo zooms, a "view" label slides up and the arrow fills. --}}
<article {{ $attributes->merge(['class' => 'group relative isolate flex flex-col overflow-hidden rounded-card border border-line bg-paper shadow-[0_1px_2px_rgba(30,26,22,0.05),0_8px_24px_-16px_rgba(30,26,22,0.25)] transition duration-500 ease-out hover:-translate-y-1.5 hover:border-gold-deep/40 hover:shadow-[0_2px_4px_rgba(30,26,22,0.05),0_28px_50px_-24px_rgba(168,115,27,0.45)] focus-within:ring-2 focus-within:ring-honey active:translate-y-0 active:scale-[.99] motion-reduce:transform-none motion-reduce:transition-none']) }}>
    {{-- Gold line drawn from the centre on hover --}}
    <span class="absolute inset-x-0 top-0 z-10 mx-auto h-0.5 w-0 bg-gradient-to-l from-transparent via-gold-deep to-transparent transition-all duration-700 ease-out group-hover:w-full" aria-hidden="true"></span>

    <div class="relative aspect-square overflow-hidden bg-cream">
        @if ($image)
            <img src="{{ $image->url() }}" alt="{{ $image->altText() }}" loading="lazy"
                 class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-out group-hover:scale-105 motion-reduce:transform-none">
        @else
            <x-image-placeholder class="h-full w-full transition duration-700 group-hover:scale-105 motion-reduce:transform-none" />
        @endif

        {{-- Soft shade at the foot of the photo, deeper on hover, so the label reads --}}
        <span class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-ink/35 to-transparent opacity-0 transition duration-500 group-hover:opacity-100" aria-hidden="true"></span>

        @if ($startingPrice !== null && ! $available)
            <span class="absolute start-3 top-3 rounded-full bg-ink/85 px-3 py-1 text-xs text-cream">
                {{ __('shop.out_of_stock') }}
            </span>
        @endif

        {{-- "View product" slides up on hover (decorative: the whole card is the link) --}}
        <span class="pointer-events-none absolute inset-x-4 bottom-4 flex translate-y-3 items-center justify-center gap-2 rounded-btn bg-cream/95 py-2 text-sm font-bold text-ink opacity-0 shadow-sm transition duration-500 group-hover:translate-y-0 group-hover:opacity-100 motion-reduce:transform-none"
              aria-hidden="true">
            {{ __('shop.view_product') }} <span>←</span>
        </span>
    </div>

    <div class="flex flex-1 flex-col gap-1 p-4 sm:p-5">
        <h3 class="text-base font-extrabold leading-7 transition-colors duration-500 group-hover:text-gold-deep">
            {{-- The link covers the whole card (after:inset-0). --}}
            <a href="{{ route('products.show', $product) }}" class="outline-none after:absolute after:inset-0">{{ $product->name }}</a>
        </h3>

        @if ($activeVariants->isNotEmpty())
            <p class="text-sm text-muted">{{ $activeVariants->pluck('title')->join('، ') }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between gap-2 border-t border-line/70 pt-3">
            <p class="font-bold text-gold-deep">
                @if ($startingPrice !== null)
                    {{ __('shop.from_price', ['price' => \App\Support\Toman::format($startingPrice)]) }}
                @else
                    <span class="font-normal text-muted">{{ __('shop.price_soon') }}</span>
                @endif
            </p>
            <span class="flex size-8 shrink-0 items-center justify-center rounded-full border border-gold-deep/30 text-gold-deep transition duration-500 group-hover:-translate-x-0.5 group-hover:border-gold-deep group-hover:bg-gold-deep group-hover:text-cream" aria-hidden="true">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
            </span>
        </div>
    </div>
</article>
