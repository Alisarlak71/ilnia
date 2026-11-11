<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Weight picker + price on the product page.
 * The "add to cart" action is connected in Phase 2 (CartService).
 */
class ProductPurchase extends Component
{
    #[Locked]
    public int $productId;

    public ?int $variantId = null;

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
    }

    public function render(): View
    {
        return view('livewire.product-purchase');
    }

    private function defaultVariant(): ?ProductVariant
    {
        return $this->variants->first(fn (ProductVariant $variant) => $variant->isPurchasable())
            ?? $this->variants->first();
    }
}
