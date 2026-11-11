{{--
    Address form fields, bound to an App\Livewire\Forms\AddressForm named "form".
    Used on the addresses page and at checkout.
--}}
@props(['provinces'])

@php
    $input = 'mt-2 w-full rounded-btn border border-line bg-cream px-4 py-2.5 focus:border-gold-deep focus:outline-none focus:ring-2 focus:ring-honey/40';
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="recipient_name" class="font-bold">{{ __('validation.attributes.recipient_name') }}</label>
        <input id="recipient_name" type="text" wire:model="form.recipient_name" autocomplete="name" class="{{ $input }}">
        @error('form.recipient_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="mobile" class="font-bold">{{ __('validation.attributes.mobile') }}</label>
        <input id="mobile" type="tel" wire:model="form.mobile" inputmode="tel" autocomplete="tel" dir="ltr" placeholder="09121234567" class="{{ $input }} text-start">
        @error('form.mobile') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="province" class="font-bold">{{ __('validation.attributes.province') }}</label>
        <select id="province" wire:model="form.province" class="{{ $input }}">
            <option value="">{{ __('account.choose_province') }}</option>
            @foreach ($provinces as $item)
                <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
        </select>
        @error('form.province') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="city" class="font-bold">{{ __('validation.attributes.city') }}</label>
        <input id="city" type="text" wire:model="form.city" autocomplete="address-level2" class="{{ $input }}">
        @error('form.city') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
    </div>
    <div class="sm:col-span-2">
        <label for="address_line" class="font-bold">{{ __('validation.attributes.address_line') }}</label>
        <textarea id="address_line" wire:model="form.address_line" rows="3" autocomplete="street-address"
                  placeholder="{{ __('account.address_line_hint') }}" class="{{ $input }}"></textarea>
        @error('form.address_line') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="postal_code" class="font-bold">{{ __('validation.attributes.postal_code') }}</label>
        <input id="postal_code" type="text" wire:model="form.postal_code" inputmode="numeric" autocomplete="postal-code" dir="ltr"
               placeholder="{{ __('account.postal_code_hint') }}" class="{{ $input }} text-start">
        @error('form.postal_code') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
    </div>
</div>
