<?php

namespace App\Livewire;

use App\Exceptions\CartException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Weight picker, quantity and "add to cart" on the product page.
 */
class ProductPurchase extends Component
{
    #[Locked]
    public int $productId;

    public ?int $variantId = null;

    public int $quantity = 1;

    public ?string $message = null;

    public ?string $error = null;

    public function mount(Product $product): void
    {
        $this->productId = $product->id;
        $this->variantId = $this->defaultVariant()?->id;
    }

    /** Active weights of this product, freshly read from the database (prices never come from the browser). */
    #[Computed]
    public function variants(): Collection
    {
        return ProductVariant::query()
            ->where('product_id', $this->productId)
            ->active()
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function selected(): ?ProductVariant
    {
        return $this->variants->firstWhere('id', $this->variantId);
    }

    /** Ignore ids that do not belong to this product (tampered requests). */
    public function updatedVariantId(): void
    {
        if ($this->selected === null) {
            $this->variantId = $this->defaultVariant()?->id;
            unset($this->selected);
        }

        $this->quantity = 1;
        $this->message = $this->error = null;
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max(1, min($this->quantity, CartService::MAX_PER_ITEM));
    }

    public function increment(): void
    {
        $this->quantity = min($this->quantity + 1, $this->maxQuantity());
    }

    public function decrement(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function addToCart(CartService $cart): void
    {
        $this->message = $this->error = null;

        if ($this->selected === null) {
            $this->error = __('cart.not_available');

            return;
        }

        try {
            $cart->add($this->selected->id, $this->quantity);
            $this->message = __('cart.added');
            $this->quantity = 1;
            $this->dispatch('cart-updated');
        } catch (CartException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render(): View
    {
        return view('livewire.product-purchase', ['maxQuantity' => $this->maxQuantity()]);
    }

    private function maxQuantity(): int
    {
        return max(1, min($this->selected?->stock ?? 1, CartService::MAX_PER_ITEM));
    }

    private function defaultVariant(): ?ProductVariant
    {
        return $this->variants->first(fn (ProductVariant $variant) => $variant->isPurchasable())
            ?? $this->variants->first();
    }
}
