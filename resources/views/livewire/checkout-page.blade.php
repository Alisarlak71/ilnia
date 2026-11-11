<div class="mx-auto max-w-5xl px-4 py-10 sm:py-14">
    <h1 class="text-center text-3xl font-extrabold">{{ __('checkout.title') }}</h1>
    <x-ornament class="mt-4" />

    <div class="mt-10 grid gap-8 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Address --}}
            <section class="rounded-card border border-line bg-paper p-5" aria-labelledby="address-title">
                <h2 id="address-title" class="text-lg font-extrabold">{{ __('checkout.address') }}</h2>

                @if ($this->addresses->isNotEmpty())
                    <div class="mt-4 space-y-3" role="radiogroup" aria-labelledby="address-title">
                        @foreach ($this->addresses as $address)
                            <label wire:key="checkout-address-{{ $address->id }}"
                                   @class([
                                       'flex cursor-pointer gap-3 rounded-btn border p-4 transition-colors has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-honey',
                                       'border-gold-deep bg-cream' => $address->id === $addressId,
                                       'border-line hover:border-gold-deep/60' => $address->id !== $addressId,
                                   ])>
                                <input type="radio" wire:model.live="addressId" value="{{ $address->id }}" class="mt-1.5 accent-[#A8731B]">
                                <span class="space-y-1">
                                    <span class="block font-bold">{{ $address->summary() }}</span>
                                    <span class="block text-sm text-muted">
                                        {{ $address->recipient_name }} · {{ \App\Support\Digits::toPersian($address->mobile) }}
                                        · {{ __('account.postal_code') }}: {{ \App\Support\Digits::toPersian($address->postal_code) }}
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-muted">{{ __('checkout.no_addresses') }}</p>
                @endif

                @if ($addingAddress)
                    <form wire:submit="saveAddress" class="mt-5 border-t border-line pt-5" novalidate>
                        <x-address-fields :provinces="$provinces" />
                        <div class="mt-5 flex flex-wrap gap-3">
                            <button type="submit" wire:loading.attr="disabled"
                                    class="rounded-btn border border-ink bg-ink px-5 py-2.5 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60">
                                {{ __('checkout.save_address') }}
                            </button>
                            @if ($this->addresses->isNotEmpty())
                                <button type="button" wire:click="cancelAddress" class="rounded-btn border border-line px-5 py-2.5 hover:border-gold-deep">
                                    {{ __('account.cancel') }}
                                </button>
                            @endif
                        </div>
                    </form>
                @else
                    <button type="button" wire:click="startAddingAddress" class="mt-4 text-sm font-bold text-gold-deep hover:underline">
                        + {{ __('checkout.add_address') }}
                    </button>
                @endif
            </section>

            {{-- Shipping --}}
            <section class="rounded-card border border-line bg-paper p-5" aria-labelledby="shipping-title">
                <h2 id="shipping-title" class="text-lg font-extrabold">{{ __('checkout.shipping') }}</h2>

                @if ($this->methods->isEmpty())
                    <p role="alert" class="mt-3 text-sm text-danger">{{ __('checkout.no_shipping') }}</p>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($this->methods as $method)
                            @php($cost = $totals ? $shipping->cost($method, $totals->weightGrams) : $method->cost)
                            <label wire:key="method-{{ $method->id }}"
                                   @class([
                                       'flex cursor-pointer items-center justify-between gap-3 rounded-btn border p-4 transition-colors has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-honey',
                                       'border-gold-deep bg-cream' => $method->id === $shippingMethodId,
                                       'border-line hover:border-gold-deep/60' => $method->id !== $shippingMethodId,
                                   ])>
                                <span class="flex items-center gap-3">
                                    <input type="radio" wire:model.live="shippingMethodId" value="{{ $method->id }}" class="accent-[#A8731B]">
                                    <span class="font-bold">{{ $method->name }}</span>
                                </span>
                                <span class="{{ $cost === 0 ? 'font-bold text-success' : '' }}">
                                    {{ $cost === 0 ? __('checkout.shipping_free') : \App\Support\Toman::format($cost) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Note --}}
            <section class="rounded-card border border-line bg-paper p-5">
                <label for="note" class="text-lg font-extrabold">{{ __('checkout.note') }}</label>
                <textarea id="note" wire:model="note" rows="2" maxlength="500" placeholder="{{ __('checkout.note_hint') }}"
                          class="mt-3 w-full rounded-btn border border-line bg-cream px-4 py-2.5 focus:border-gold-deep focus:outline-none focus:ring-2 focus:ring-honey/40"></textarea>
            </section>
        </div>

        {{-- Summary --}}
        <aside class="h-fit rounded-card border border-line bg-paper p-5 lg:sticky lg:top-6" aria-labelledby="summary-title">
            <div class="flex items-center justify-between">
                <h2 id="summary-title" class="text-lg font-extrabold">{{ __('checkout.summary') }}</h2>
                <a href="{{ route('cart') }}" class="text-sm text-gold-deep hover:underline">{{ __('checkout.edit_cart') }}</a>
            </div>

            <ul class="mt-4 space-y-2 border-b border-line pb-4 text-sm">
                @foreach ($lines as $line)
                    <li wire:key="sum-{{ $line->variant->id }}" class="flex justify-between gap-3">
                        <span>{{ $line->variant->product->name }} ({{ $line->variant->title }}) × {{ \App\Support\Digits::toPersian($line->quantity) }}</span>
                        <span class="shrink-0">{{ \App\Support\Toman::format($line->lineTotal) }}</span>
                    </li>
                @endforeach
            </ul>

            @if ($totals)
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt>{{ __('checkout.subtotal') }}</dt>
                        <dd>{{ \App\Support\Toman::format($totals->subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('checkout.shipping_cost') }}</dt>
                        <dd class="{{ $totals->shippingCost === 0 ? 'font-bold text-success' : '' }}">
                            {{ $totals->shippingCost === 0 ? __('checkout.shipping_free') : \App\Support\Toman::format($totals->shippingCost) }}
                        </dd>
                    </div>
                    <div class="flex justify-between border-t border-line pt-3 text-base">
                        <dt class="font-bold">{{ __('checkout.total') }}</dt>
                        <dd class="text-xl font-extrabold text-gold-deep">{{ \App\Support\Toman::format($totals->total) }}</dd>
                    </div>
                </dl>
                <p class="mt-2 text-xs text-muted">{{ __('checkout.weight', ['weight' => \App\Support\Weight::kg($totals->weightGrams)]) }}</p>
            @endif

            {{-- Order creation and payment are connected in the next roadmap steps. --}}
            <button type="button" disabled
                    class="mt-5 w-full cursor-not-allowed rounded-btn bg-ink/40 px-6 py-3 font-bold text-cream">
                {{ __('checkout.pay') }}
            </button>
            <p class="mt-2 text-center text-xs text-muted">{{ __('checkout.payment_soon') }}</p>
        </aside>
    </div>
</div>
