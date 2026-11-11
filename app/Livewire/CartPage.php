<?php

namespace App\Livewire;

use App\Exceptions\CartException;
use App\Services\CartService;
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

    public function render(CartService $cart): View
    {
        $lines = $cart->lines();

        return view('livewire.cart-page', [
            'lines' => $lines,
            'subtotal' => (int) $lines->sum('lineTotal'),
            'adjustments' => $cart->pullAdjustments(),
        ]);
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
