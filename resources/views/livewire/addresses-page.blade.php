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

            <div class="mt-5">
                <x-address-fields :provinces="$provinces" />
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
