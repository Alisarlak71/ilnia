<?php

namespace App\Livewire\Forms;

use App\Models\Address;
use App\Models\User;
use App\Support\Digits;
use App\Support\IranProvinces;
use App\Support\Mobile;
use Closure;
use Illuminate\Validation\Rule;
use Livewire\Form;

/**
 * Address fields + normalization + validation, shared by the addresses page and checkout.
 * In views: wire:model="form.city", errors: @error('form.city').
 */
class AddressForm extends Form
{
    public string $recipient_name = '';

    public string $mobile = '';

    public string $province = '';

    public string $city = '';

    public string $address_line = '';

    public string $postal_code = '';

    public function prefillFor(User $user): void
    {
        $this->reset();
        $this->recipient_name = (string) $user->name;
        $this->mobile = (string) $user->mobile;
    }

    public function fillFrom(Address $address): void
    {
        $this->fill($address->only(['recipient_name', 'mobile', 'province', 'city', 'address_line', 'postal_code']));
    }

    /**
     * Normalize the input (Persian digits, spaces, +98…) and validate.
     *
     * @return array<string, string> clean data ready to save
     */
    public function validated(): array
    {
        $this->mobile = Mobile::normalize($this->mobile) ?? trim($this->mobile);
        $this->postal_code = preg_replace('/\D/', '', Digits::toEnglish($this->postal_code));
        foreach (['recipient_name', 'city', 'address_line'] as $field) {
            $this->{$field} = trim(preg_replace('/\s+/u', ' ', $this->{$field}));
        }

        return $this->validate();
    }

    protected function rules(): array
    {
        return [
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
        ];
    }

    protected function validationAttributes(): array
    {
        return collect(['recipient_name', 'mobile', 'province', 'city', 'address_line', 'postal_code'])
            ->mapWithKeys(fn ($field) => [$field => __('validation.attributes.'.$field)])
            ->all();
    }

    protected function messages(): array
    {
        return ['province.required' => __('validation.custom.province.required')];
    }
}
