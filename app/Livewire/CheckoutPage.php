<?php

namespace App\Livewire;

use App\Livewire\Forms\AddressForm;
use App\Models\Address;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderPricing;
use App\Services\OrderTotals;
use App\Services\ShippingCalculator;
use App\Support\Digits;
use App\Support\IranProvinces;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Checkout (/checkout, logged-in customers only): address, shipping method, note, summary.
 * Amounts are always recalculated on the server (CartService + OrderPricing).
 * Placing the order and paying are connected in the next roadmap steps.
 */
#[Title('تسویه‌حساب')]
class CheckoutPage extends Component
{
    public AddressForm $form;

    public ?int $addressId = null;

    public ?int $shippingMethodId = null;

    public string $note = '';

    public bool $addingAddress = false;

    public function mount(CartService $cart, ShippingCalculator $shipping)
    {
        if ($cart->isEmpty()) {
            return $this->redirectRoute('cart', navigate: false);
        }

        $this->addressId = auth()->user()->addresses()->value('id');
        $this->shippingMethodId = $shipping->defaultMethod()?->id;
        $this->addingAddress = $this->addressId === null;

        if ($this->addingAddress) {
            $this->form->prefillFor(auth()->user());
        }

        return null;
    }

    /** @return Collection<int, Address> */
    #[Computed]
    public function addresses(): Collection
    {
        return auth()->user()->addresses()->get();
    }

    /** Only the customer's own address can be selected; anything else counts as "none". */
    #[Computed]
    public function selectedAddress(): ?Address
    {
        return $this->addresses->firstWhere('id', $this->addressId);
    }

    /** @return Collection<int, ShippingMethod> */
    #[Computed]
    public function methods(): Collection
    {
        return ShippingMethod::query()->active()->get();
    }

    #[Computed]
    public function selectedMethod(): ?ShippingMethod
    {
        return $this->methods->firstWhere('id', $this->shippingMethodId);
    }

    public function startAddingAddress(): void
    {
        $this->form->prefillFor(auth()->user());
        $this->resetValidation();
        $this->addingAddress = true;
    }

    public function cancelAddress(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->addingAddress = $this->addresses->isEmpty();
    }

    public function saveAddress(): void
    {
        $user = auth()->user();

        if ($user->addresses()->count() >= AddressesPage::MAX_ADDRESSES) {
            $this->addError('form.recipient_name', __('account.max_addresses', ['max' => Digits::toPersian(AddressesPage::MAX_ADDRESSES)]));

            return;
        }

        $address = $user->addresses()->create($this->form->validated());

        $this->form->reset();
        $this->addingAddress = false;
        $this->addressId = $address->id;
        unset($this->addresses, $this->selectedAddress);
    }

    public function render(CartService $cart, OrderPricing $pricing): View
    {
        $lines = $cart->lines();
        $method = $this->selectedMethod;

        /** @var OrderTotals|null $totals */
        $totals = $method && $lines->isNotEmpty() ? $pricing->quote($lines, $method) : null;

        return view('livewire.checkout-page', [
            'lines' => $lines,
            'totals' => $totals,
            'provinces' => IranProvinces::all(),
            'shipping' => app(ShippingCalculator::class),
        ]);
    }
}
