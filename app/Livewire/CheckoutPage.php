<?php

namespace App\Livewire;

use App\Exceptions\OrderException;
use App\Exceptions\PaymentException;
use App\Livewire\Forms\AddressForm;
use App\Models\Address;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderPricing;
use App\Services\OrderService;
use App\Services\OrderTotals;
use App\Services\PaymentService;
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
 *
 * pay(): creates the order (OrderService) and sends the customer to Zarinpal (PaymentService).
 * While ZARINPAL_MERCHANT_ID is empty, online payment is off: the button is disabled and
 * NO order is created, even if pay() is called directly.
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

    public function pay(OrderService $orders, PaymentService $payments)
    {
        $this->resetErrorBag('pay');

        if (! $payments->isEnabled()) {
            $this->addError('pay', __('payment.disabled'));

            return null;
        }

        if ($this->addingAddress || ! $this->selectedAddress) {
            $this->addError('pay', __('order.invalid_address'));

            return null;
        }

        try {
            $order = $orders->createFromCart(auth()->user(), $this->selectedAddress->id, (int) $this->shippingMethodId, $this->note);
        } catch (OrderException $e) {
            // The cart is empty or changed: the cart page shows what changed and why.
            if (in_array($e->getMessage(), [__('order.cart_changed'), __('order.cart_empty')], true)) {
                session()->flash('cart_notice', $e->getMessage());

                return $this->redirectRoute('cart', navigate: false);
            }

            $this->addError('pay', $e->getMessage());

            return null;
        }

        try {
            return $this->redirect($payments->request($order), navigate: false);
        } catch (PaymentException $e) {
            // The order is saved (payment_failed); the customer can pay it again later.
            session()->flash('payment_error', $e->getMessage());

            return $this->redirectRoute('payment.result', $order, navigate: false);
        }
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
            'paymentEnabled' => app(PaymentService::class)->isEnabled(),
        ]);
    }
}
