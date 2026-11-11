<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Cart icon with item count in the header. Refreshes when a "cart-updated" event is dispatched.
 */
class CartIcon extends Component
{
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
