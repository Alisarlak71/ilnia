<?php

namespace App\Livewire;

use App\Models\Address;
use App\Support\Digits;
use App\Support\IranProvinces;
use App\Support\Mobile;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
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

    public bool $showForm = false;

    /** Address being edited (null = new). Set only by the server. */
    #[Locked]
    public ?int $editingId = null;

    public string $recipient_name = '';

    public string $mobile = '';

    public string $province = '';

    public string $city = '';

    public string $address_line = '';

    public string $postal_code = '';

    public ?string $message = null;

    public function create(): void
    {
        $this->resetForm();
        $this->message = null;
        $this->recipient_name = (string) auth()->user()->name;
        $this->mobile = (string) auth()->user()->mobile;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $address = auth()->user()->addresses()->findOrFail($id);

        $this->resetForm();
        $this->message = null;
        $this->editingId = $address->id;
        $this->fill($address->only(['recipient_name', 'mobile', 'province', 'city', 'address_line', 'postal_code']));
        $this->showForm = true;
    }

    public function save(): void
    {
        $user = auth()->user();

        if ($this->editingId === null && $user->addresses()->count() >= self::MAX_ADDRESSES) {
            $this->addError('recipient_name', __('account.max_addresses', ['max' => Digits::toPersian(self::MAX_ADDRESSES)]));

            return;
        }

        // Normalize user input before validating: Persian digits, spaces, dashes, +98…
        $this->mobile = Mobile::normalize($this->mobile) ?? trim($this->mobile);
        $this->postal_code = preg_replace('/\D/', '', Digits::toEnglish($this->postal_code));
        foreach (['recipient_name', 'city', 'address_line'] as $field) {
            $this->{$field} = trim(preg_replace('/\s+/u', ' ', $this->{$field}));
        }

        $data = $this->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', function (string $attribute, mixed $value, Closure $fail) {
                if (! Mobile::isValid($value)) {
                    $fail(__('otp.invalid_mobile'));
                }
            }],
            'province' => ['required', Rule::in(IranProvinces::all())],
            'city' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'min:10', 'max:500'],
            'postal_code' => ['required', 'digits:10'],
        ]);

        if ($this->editingId !== null) {
            $user->addresses()->findOrFail($this->editingId)->update($data);
            $this->message = __('account.address_updated');
        } else {
            $user->addresses()->create($data);
            $this->message = __('account.address_added');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        auth()->user()->addresses()->findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->message = __('account.address_deleted');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        /** @var Collection<int, Address> $addresses */
        $addresses = auth()->user()->addresses()->get();

        return view('livewire.addresses-page', [
            'addresses' => $addresses,
            'provinces' => IranProvinces::all(),
            'canAdd' => $addresses->count() < self::MAX_ADDRESSES,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['showForm', 'editingId', 'recipient_name', 'mobile', 'province', 'city', 'address_line', 'postal_code']);
        $this->resetValidation();
    }
}
