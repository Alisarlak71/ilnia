<div class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
    <h1 class="text-center text-3xl font-extrabold">{{ __('account.addresses_title') }}</h1>
    <x-ornament class="mt-4" />

    @if ($message)
        <p role="status" class="mt-6 rounded-btn border border-success/30 bg-success/10 px-4 py-3 text-sm text-success">{{ $message }}</p>
    @endif

    {{-- List --}}
    @if ($addresses->isEmpty() && ! $showForm)
        <div class="mx-auto mt-8 max-w-md rounded-card border border-line bg-paper p-8 text-center">
            <p class="text-muted">{{ __('account.addresses_empty') }}</p>
        </div>
    @endif

    <ul class="mt-8 space-y-4">
        @foreach ($addresses as $address)
            <li wire:key="address-{{ $address->id }}"
                @class(['rounded-card border bg-paper p-5', 'border-gold-deep' => $editingId === $address->id, 'border-line' => $editingId !== $address->id])>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1">
                        <p class="font-bold">{{ $address->summary() }}</p>
                        <p class="text-sm text-muted">
                            {{ __('account.recipient') }}: {{ $address->recipient_name }} · {{ \App\Support\Digits::toPersian($address->mobile) }}
                        </p>
                        <p class="text-sm text-muted">{{ __('account.postal_code') }}: {{ \App\Support\Digits::toPersian($address->postal_code) }}</p>
                    </div>
                    <div class="flex gap-4 text-sm">
                        <button type="button" wire:click="edit({{ $address->id }})" class="text-gold-deep hover:underline">{{ __('account.edit') }}</button>
                        <button type="button" wire:click="delete({{ $address->id }})" wire:confirm="{{ __('account.delete_confirm') }}"
                                class="text-muted hover:text-danger hover:underline">{{ __('account.delete') }}</button>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    {{-- Form --}}
    @if ($showForm)
        <form wire:submit="save" class="mt-8 rounded-card border border-line bg-paper p-5 sm:p-6" novalidate>
            <h2 class="text-lg font-extrabold">{{ $editingId ? __('account.edit_address') : __('account.new_address') }}</h2>

            @php
                $input = 'mt-2 w-full rounded-btn border border-line bg-cream px-4 py-2.5 focus:border-gold-deep focus:outline-none focus:ring-2 focus:ring-honey/40';
            @endphp

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="recipient_name" class="font-bold">{{ __('validation.attributes.recipient_name') }}</label>
                    <input id="recipient_name" type="text" wire:model="recipient_name" autocomplete="name" class="{{ $input }}">
                    @error('recipient_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="mobile" class="font-bold">{{ __('validation.attributes.mobile') }}</label>
                    <input id="mobile" type="tel" wire:model="mobile" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="09121234567" class="{{ $input }} text-start">
                    @error('mobile') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="province" class="font-bold">{{ __('validation.attributes.province') }}</label>
                    <select id="province" wire:model="province" class="{{ $input }}">
                        <option value="">{{ __('account.choose_province') }}</option>
                        @foreach ($provinces as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('province') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="city" class="font-bold">{{ __('validation.attributes.city') }}</label>
                    <input id="city" type="text" wire:model="city" autocomplete="address-level2" class="{{ $input }}">
                    @error('city') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="address_line" class="font-bold">{{ __('validation.attributes.address_line') }}</label>
                    <textarea id="address_line" wire:model="address_line" rows="3" autocomplete="street-address"
                              placeholder="{{ __('account.address_line_hint') }}" class="{{ $input }}"></textarea>
                    @error('address_line') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="postal_code" class="font-bold">{{ __('validation.attributes.postal_code') }}</label>
                    <input id="postal_code" type="text" wire:model="postal_code" inputmode="numeric" autocomplete="postal-code" dir="ltr"
                           placeholder="{{ __('account.postal_code_hint') }}" class="{{ $input }} text-start">
                    @error('postal_code') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" wire:loading.attr="disabled"
                        class="rounded-btn border border-ink bg-ink px-6 py-2.5 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60">
                    {{ __('account.save') }}
                </button>
                <button type="button" wire:click="cancel" class="rounded-btn border border-line px-6 py-2.5 hover:border-gold-deep">
                    {{ __('account.cancel') }}
                </button>
            </div>
        </form>
    @elseif ($canAdd)
        <div class="mt-8 text-center">
            <button type="button" wire:click="create"
                    class="rounded-btn border border-gold-deep px-6 py-3 font-bold transition-colors hover:bg-gold-deep hover:text-cream">
                + {{ __('account.add_address') }}
            </button>
        </div>
    @endif
</div>
