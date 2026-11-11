<div class="mx-auto max-w-5xl px-4 py-10 sm:py-14">
    <h1 class="text-center text-3xl font-extrabold">{{ __('cart.title') }}</h1>
    <x-ornament class="mt-4" />

    {{-- Automatic corrections (unavailable items, reduced quantities) --}}
    @foreach ($adjustments as $message)
        <p wire:key="adj-{{ $loop->index }}" role="status"
           class="mt-6 rounded-btn border border-honey/40 bg-honey/10 px-4 py-3 text-sm">{{ $message }}</p>
    @endforeach

    @if ($error)
        <p role="alert" class="mt-6 rounded-btn border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">{{ $error }}</p>
    @endif

    @if ($lines->isEmpty())
        <div class="mx-auto mt-10 max-w-md rounded-card border border-line bg-paper p-8 text-center">
            <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="56" height="56" alt="" class="mx-auto opacity-40">
            <h2 class="mt-4 text-lg font-bold">{{ __('cart.empty_title') }}</h2>
            <p class="mt-2 text-muted">{{ __('cart.empty_text') }}</p>
            <a href="{{ route('products.index') }}"
               class="mt-6 inline-block rounded-btn border border-ink bg-ink px-6 py-3 font-bold text-cream transition-colors hover:border-gold">
                {{ __('cart.browse_products') }}
            </a>
        </div>
    @else
        <div class="mt-10 grid gap-8 lg:grid-cols-3">
            {{-- Lines --}}
            <ul class="space-y-4 lg:col-span-2">
                @foreach ($lines as $line)
                    @php
                        $variant = $line->variant;
                        $product = $variant->product;
                        $image = $product->mainImage();
                        $label = $product->name.' ('.$variant->title.')';
                    @endphp
                    <li wire:key="line-{{ $variant->id }}" class="flex gap-4 rounded-card border border-line bg-paper p-4">
                        <a href="{{ route('products.show', $product) }}" class="block size-20 shrink-0 overflow-hidden rounded-btn sm:size-24">
                            @if ($image)
                                <img src="{{ $image->url() }}" alt="{{ $image->altText() }}" class="h-full w-full object-cover">
                            @else
                                <x-image-placeholder class="h-full w-full" />
                            @endif
                        </a>

                        <div class="flex flex-1 flex-col gap-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('products.show', $product) }}" class="font-bold hover:text-gold-deep">{{ $product->name }}</a>
                                    <p class="text-sm text-muted">{{ $variant->title }} · {{ __('cart.unit_price') }}: {{ \App\Support\Toman::format($line->unitPrice) }}</p>
                                </div>
                                <button type="button" wire:click="remove({{ $variant->id }})"
                                        class="shrink-0 text-sm text-muted underline-offset-4 hover:text-danger hover:underline"
                                        aria-label="{{ __('cart.remove_label', ['name' => $label]) }}">
                                    {{ __('cart.remove') }}
                                </button>
                            </div>

                            <div class="mt-auto flex flex-wrap items-center justify-between gap-3">
                                <div class="inline-flex items-center rounded-btn border border-line bg-cream" role="group" aria-label="{{ __('cart.quantity') }}">
                                    <button type="button" wire:click="increment({{ $variant->id }})"
                                            class="px-3 py-1.5 text-lg leading-none hover:text-gold-deep disabled:opacity-30"
                                            @disabled($line->quantity >= min($variant->stock, \App\Services\CartService::MAX_PER_ITEM))
                                            aria-label="{{ __('cart.increase', ['name' => $label]) }}">+</button>
                                    <span class="min-w-8 text-center font-bold" aria-live="polite">{{ \App\Support\Digits::toPersian($line->quantity) }}</span>
                                    <button type="button" wire:click="decrement({{ $variant->id }})"
                                            class="px-3 py-1.5 text-lg leading-none hover:text-gold-deep"
                                            aria-label="{{ __('cart.decrease', ['name' => $label]) }}">−</button>
                                </div>
                                <p class="font-bold text-gold-deep">{{ \App\Support\Toman::format($line->lineTotal) }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Summary --}}
            <aside class="h-fit rounded-card border border-line bg-paper p-5 lg:sticky lg:top-6">
                <div class="flex items-center justify-between">
                    <span>{{ __('cart.subtotal') }}</span>
                    <span class="text-xl font-extrabold">{{ \App\Support\Toman::format($subtotal) }}</span>
                </div>
                <p class="mt-2 text-xs text-muted">{{ __('cart.shipping_note') }}</p>

                @if ($freeShipping)
                    <div class="mt-4 rounded-btn border px-3 py-3 text-sm {{ $freeShipping['free'] ? 'border-success/30 bg-success/10 text-success' : 'border-line bg-cream' }}"
                         role="status">
                        <p>{{ $freeShipping['message'] }}</p>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-line" aria-hidden="true">
                            <div class="h-full rounded-full {{ $freeShipping['free'] ? 'bg-success' : 'bg-gold-deep' }}"
                                 style="width: {{ $freeShipping['percent'] }}%"></div>
                        </div>
                    </div>
                @endif

                {{-- Enabled in Phase 3 (checkout). --}}
                <button type="button" disabled
                        class="mt-5 w-full cursor-not-allowed rounded-btn bg-ink/40 px-6 py-3 font-bold text-cream">
                    {{ __('cart.checkout') }}
                </button>
                <p class="mt-2 text-center text-xs text-muted">{{ __('cart.checkout_soon') }}</p>

                <a href="{{ route('products.index') }}" class="mt-4 block text-center text-sm text-gold-deep hover:underline">
                    {{ __('cart.continue_shopping') }}
                </a>
            </aside>
        </div>
    @endif
</div>
