<?php

namespace App\Livewire;

use App\Livewire\Forms\AddressForm;
use App\Support\Digits;
use App\Support\IranProvinces;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Customer's saved addresses (/account/addresses): list, add, edit, delete.
 * Every lookup goes through auth()->user()->addresses(), so nobody can reach another customer's address.
 */
#[Title('آدرس‌های من')]
class AddressesPage extends Component
{
    public const MAX_ADDRESSES = 10;

    public AddressForm $form;

    public bool $showForm = false;

    /** Address being edited (null = new). Set only by the server. */
    #[Locked]
    public ?int $editingId = null;

    public ?string $message = null;

    public function create(): void
    {
        $this->closeForm();
        $this->message = null;
        $this->form->prefillFor(auth()->user());
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $address = auth()->user()->addresses()->findOrFail($id);

        $this->closeForm();
        $this->message = null;
        $this->editingId = $address->id;
        $this->form->fillFrom($address);
        $this->showForm = true;
    }

    public function save(): void
    {
        $user = auth()->user();

        if ($this->editingId === null && $user->addresses()->count() >= self::MAX_ADDRESSES) {
            $this->addError('form.recipient_name', __('account.max_addresses', ['max' => Digits::toPersian(self::MAX_ADDRESSES)]));

            return;
        }

        $data = $this->form->validated();

        if ($this->editingId !== null) {
            $user->addresses()->findOrFail($this->editingId)->update($data);
            $this->message = __('account.address_updated');
        } else {
            $user->addresses()->create($data);
            $this->message = __('account.address_added');
        }

        $this->closeForm();
    }

    public function delete(int $id): void
    {
        auth()->user()->addresses()->findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->closeForm();
        }

        $this->message = __('account.address_deleted');
    }

    public function cancel(): void
    {
        $this->closeForm();
    }

    public function render(): View
    {
        $addresses = auth()->user()->addresses()->get();

        return view('livewire.addresses-page', [
            'addresses' => $addresses,
            'provinces' => IranProvinces::all(),
            'canAdd' => $addresses->count() < self::MAX_ADDRESSES,
        ]);
    }

    private function closeForm(): void
    {
        $this->reset(['showForm', 'editingId']);
        $this->form->reset();
        $this->resetValidation();
    }
}
