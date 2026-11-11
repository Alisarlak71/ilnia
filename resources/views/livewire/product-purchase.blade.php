<div class="rounded-card border border-line bg-paper p-5">
    @if ($this->variants->isEmpty())
        <p class="text-muted">{{ __('shop.price_soon') }}</p>
    @else
        <fieldset>
            <legend class="font-bold">{{ __('shop.choose_weight') }}</legend>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($this->variants as $variant)
                    <label wire:key="variant-{{ $variant->id }}"
                           @class([
                               'cursor-pointer rounded-btn border px-4 py-2 text-sm transition-colors has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-honey',
                               'border-ink bg-ink text-cream' => $variant->id === $variantId,
                               'border-line bg-cream hover:border-gold-deep' => $variant->id !== $variantId,
                           ])>
                        <input type="radio" wire:model.live="variantId" value="{{ $variant->id }}" class="sr-only">
                        {{ $variant->title }}
                        @unless ($variant->isPurchasable())
                            <span class="opacity-70">({{ $variant->hasPrice() ? __('shop.out_of_stock') : __('shop.price_soon') }})</span>
                        @endunless
                    </label>
                @endforeach
            </div>
        </fieldset>

        @php($selected = $this->selected)

        <div class="mt-5 border-t border-line pt-5" aria-live="polite">
            @if ($selected?->hasPrice())
                <p class="text-2xl font-extrabold text-gold-deep">{{ \App\Support\Toman::format($selected->price) }}</p>
                @if (! $selected->inStock())
                    <p class="mt-1 text-sm text-danger">{{ __('shop.out_of_stock') }}</p>
                @endif
            @else
                <p class="text-lg text-muted">{{ __('shop.price_soon') }}</p>
            @endif
        </div>

        {{-- Connected to the cart in Phase 2. --}}
        <button type="button" disabled
                class="mt-5 w-full cursor-not-allowed rounded-btn bg-ink/40 px-6 py-3 font-bold text-cream">
            {{ __('shop.add_to_cart') }}
        </button>
        <p class="mt-2 text-center text-xs text-muted">{{ __('shop.cart_coming_soon') }}</p>
    @endif
</div>
