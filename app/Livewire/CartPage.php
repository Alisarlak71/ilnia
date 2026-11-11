<?php

namespace App\Livewire;

use App\Exceptions\CartException;
use App\Services\CartService;
use App\Services\ShippingCalculator;
use App\Support\Toman;
use App\Support\Weight;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Full cart page (/cart): change quantities, remove items, see the subtotal.
 * All amounts come from CartService (database), never from the browser.
 */
#[Title('سبد خرید')]
class CartPage extends Component
{
    public ?string $error = null;

    public function increment(int $variantId): void
    {
        $this->change($variantId, +1);
    }

    public function decrement(int $variantId): void
    {
        $this->change($variantId, -1);
    }

    public function remove(int $variantId, CartService $cart): void
    {
        $cart->remove($variantId);
        $this->error = null;
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cart, ShippingCalculator $shipping): View
    {
        $lines = $cart->lines();

        return view('livewire.cart-page', [
            'lines' => $lines,
            'subtotal' => (int) $lines->sum('lineTotal'),
            // A notice from checkout (cart changed before paying) + automatic corrections.
            'adjustments' => array_values(array_filter([session()->pull('cart_notice'), ...$cart->pullAdjustments()])),
            'freeShipping' => $this->freeShippingInfo($shipping, (int) $lines->sum('weightGrams')),
        ]);
    }

    /**
     * Encouragement message from the real threshold in the panel.
     * Null when there is no active method or it has no free-shipping threshold.
     *
     * @return array{message: string, free: bool, percent: int}|null
     */
    private function freeShippingInfo(ShippingCalculator $shipping, int $weightGrams): ?array
    {
        $method = $shipping->defaultMethod();
        $remaining = $method ? $shipping->remainingForFree($method, $weightGrams) : null;

        if ($method === null || $remaining === null || $weightGrams === 0) {
            return null;
        }

        $message = match (true) {
            $remaining === 0 => __('cart.free_shipping_reached'),
            $remaining >= 1000 => __('cart.free_shipping_remaining_kg', ['amount' => Weight::kg($remaining)]),
            default => __('cart.free_shipping_remaining_g', ['amount' => Toman::number($remaining)]),
        };

        return [
            'message' => $message,
            'free' => $remaining === 0,
            'percent' => (int) min(100, floor($weightGrams * 100 / max(1, $method->free_over_weight_grams))),
        ];
    }

    private function change(int $variantId, int $delta): void
    {
        $cart = app(CartService::class);
        $current = $cart->raw()[$variantId] ?? null;

        // Only items that are already in the cart can be changed here.
        if ($current === null) {
            return;
        }

        try {
            $cart->setQuantity($variantId, max(0, $current + $delta));
            $this->error = null;
        } catch (CartException $e) {
            $this->error = $e->getMessage();
        }

        $this->dispatch('cart-updated');
    }
}
