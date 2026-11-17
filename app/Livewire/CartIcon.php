<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Cart link with item count. Refreshes when a "cart-updated" event is dispatched.
 * Two looks: "header" (icon in the top bar, desktop) and "tab" (icon + label in the phone bottom bar).
 */
class CartIcon extends Component
{
    public string $variant = 'header';

    #[On('cart-updated')]
    public function refreshCount(): void
    {
        // Re-render only; the count is read fresh in render().
    }

    public function render(CartService $cart): View
    {
        return view('livewire.cart-icon', ['count' => $cart->count()]);
    }
}
