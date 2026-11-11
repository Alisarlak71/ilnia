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

        @if ($selected?->isPurchasable())
            <div class="mt-5 flex items-stretch gap-3">
                <div class="inline-flex items-center rounded-btn border border-line bg-cream" role="group" aria-label="{{ __('cart.quantity') }}">
                    <button type="button" wire:click="increment" @disabled($quantity >= $maxQuantity)
                            class="px-3 py-2 text-lg leading-none hover:text-gold-deep disabled:opacity-30"
                            aria-label="{{ __('cart.increase', ['name' => $selected->title]) }}">+</button>
                    <span class="min-w-8 text-center font-bold" aria-live="polite">{{ \App\Support\Digits::toPersian($quantity) }}</span>
                    <button type="button" wire:click="decrement" @disabled($quantity <= 1)
                            class="px-3 py-2 text-lg leading-none hover:text-gold-deep disabled:opacity-30"
                            aria-label="{{ __('cart.decrease', ['name' => $selected->title]) }}">−</button>
                </div>

                <button type="button" wire:click="addToCart" wire:loading.attr="disabled"
                        class="flex-1 rounded-btn border border-ink bg-ink px-6 py-3 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60">
                    {{ __('shop.add_to_cart') }}
                </button>
            </div>
        @else
            <button type="button" disabled
                    class="mt-5 w-full cursor-not-allowed rounded-btn bg-ink/40 px-6 py-3 font-bold text-cream">
                {{ __('shop.add_to_cart') }}
            </button>
        @endif

        @if ($message)
            <p role="status" class="mt-3 flex flex-wrap items-center justify-between gap-2 rounded-btn border border-success/30 bg-success/10 px-4 py-2 text-sm text-success">
                <span>{{ $message }}</span>
                <a href="{{ route('cart') }}" class="font-bold underline underline-offset-4">{{ __('cart.view_cart') }}</a>
            </p>
        @endif

        @if ($error)
            <p role="alert" class="mt-3 rounded-btn border border-danger/30 bg-danger/10 px-4 py-2 text-sm text-danger">{{ $error }}</p>
        @endif
    @endif
</div>
