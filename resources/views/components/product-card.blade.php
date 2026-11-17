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

{{-- Shop card: photo, name, weights as small chips, a clear price and an always-visible "choose and buy"
     button (phones have no hover). On hover the card lifts, a gold line draws across the top and the photo zooms. --}}
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

        @if ($startingPrice !== null && ! $available)
            <span class="absolute start-3 top-3 rounded-full bg-ink/85 px-3 py-1 text-xs text-cream">
                {{ __('shop.out_of_stock') }}
            </span>
        @endif
    </div>

    {{-- Every row below has a fixed height (title: always 2 lines, weights: 1 line, price: 1 line), so all
         cards are exactly the same size whatever the name length, number of weights or price. --}}
    <div class="flex flex-1 flex-col p-3 sm:p-4">
        <h3 class="line-clamp-2 min-h-12 text-sm font-extrabold leading-6 transition-colors duration-500 group-hover:text-gold-deep sm:min-h-14 sm:text-base sm:leading-7">
            {{-- The link covers the whole card (after:inset-0). --}}
            <a href="{{ route('products.show', $product) }}" title="{{ $product->name }}" class="outline-none after:absolute after:inset-0">{{ $product->name }}</a>
        </h3>

        {{-- Weights: one line only; extra ones become "+n" --}}
        <ul class="mt-2 flex h-6 flex-nowrap items-center gap-1 overflow-hidden" aria-label="{{ __('shop.choose_weight') }}">
            @foreach ($activeVariants->take(2) as $variant)
                <li class="shrink-0 whitespace-nowrap rounded-full border border-line bg-cream px-2 text-[0.7rem] leading-5 text-muted">{{ $variant->title }}</li>
            @endforeach
            @if ($activeVariants->count() > 2)
                <li class="shrink-0 px-1 text-[0.7rem] leading-5 text-muted">+{{ \App\Support\Digits::toPersian($activeVariants->count() - 2) }}</li>
            @endif
        </ul>

        <div class="mt-auto pt-2">
            <p class="flex h-7 items-center truncate text-base font-black text-ink sm:h-8 sm:text-lg">
                @if ($startingPrice !== null)
                    {{ __('shop.from_price', ['price' => \App\Support\Toman::format($startingPrice)]) }}
                @else
                    <span class="text-sm font-normal text-muted">{{ __('shop.price_soon') }}</span>
                @endif
            </p>

            {{-- Decorative: the whole card is already the link --}}
            <span @class([
                    'mt-3 flex items-center justify-center gap-2 rounded-btn py-2 text-sm font-bold transition-colors duration-300',
                    'border border-transparent bg-ink text-cream group-hover:bg-gold-deep' => $available, // same border width as the other state, so heights match
                    'border border-line text-muted' => ! $available,
                  ]) aria-hidden="true">
                @if ($available)
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7V6a3 3 0 0 1 6 0v1"/></svg>
                    {{ __('shop.choose_and_buy') }}
                @else
                    {{ $startingPrice !== null ? __('shop.out_of_stock') : __('shop.notify_soon') }}
                @endif
            </span>
        </div>
    </div>
</article>
