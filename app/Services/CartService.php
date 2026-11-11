<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\ProductVariant;
use App\Support\Digits;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Session-based shopping cart.
 *
 * The session only stores `variant_id => quantity`. Prices, names, stock and weights
 * are ALWAYS read from the database, so nothing the browser sends can change a price.
 *
 * Every read (lines(), subtotal(), …) re-checks the cart against the database:
 * unavailable items are removed and quantities above stock are reduced. What changed
 * is remembered and can be shown once with pullAdjustments().
 *
 * The session survives login, so a guest's cart stays after signing in.
 */
class CartService
{
    public const SESSION_KEY = 'cart';

    public const ADJUSTMENTS_KEY = 'cart_adjustments';

    /** Upper limit per item, even when there is more stock. */
    public const MAX_PER_ITEM = 50;

    public function __construct(private readonly Session $session)
    {
    }

    /**
     * Add a quantity of a variant (adds to what is already in the cart).
     *
     * @throws CartException
     */
    public function add(int $variantId, int $quantity = 1): void
    {
        $this->assertPositive($quantity);

        $variant = $this->findPurchasable($variantId);
        $current = $this->raw()[$variantId] ?? 0;

        $this->put($variant, $current + $quantity);
    }

    /**
     * Set the exact quantity of a variant. Zero removes it.
     *
     * @throws CartException
     */
    public function setQuantity(int $variantId, int $quantity): void
    {
        if ($quantity === 0) {
            $this->remove($variantId);

            return;
        }

        $this->assertPositive($quantity);

        $this->put($this->findPurchasable($variantId), $quantity);
    }

    public function remove(int $variantId): void
    {
        $items = $this->raw();
        unset($items[$variantId]);
        $this->store($items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * Cart rows with fresh prices from the database.
     *
     * @return Collection<int, CartLine>
     */
    public function lines(): Collection
    {
        $items = $this->raw();

        if ($items === []) {
            return collect();
        }

        $variants = ProductVariant::query()
            ->with(['product.images'])
            ->whereKey(array_keys($items))
            ->get()
            ->keyBy('id');

        $lines = collect();
        $clean = [];

        foreach ($items as $variantId => $quantity) {
            $variant = $variants->get($variantId);

            if (! $variant || ! $this->isSellable($variant)) {
                $this->rememberAdjustment($variant
                    ? __('cart.adjusted_removed', ['name' => $this->label($variant)])
                    : __('cart.adjusted_removed_unknown'));

                continue;
            }

            $allowed = min($quantity, $variant->stock, self::MAX_PER_ITEM);

            if ($allowed < $quantity) {
                $this->rememberAdjustment(__('cart.adjusted_quantity', [
                    'name' => $this->label($variant),
                    'quantity' => Digits::toPersian($allowed),
                ]));
            }

            $clean[$variantId] = $allowed;
            $lines->push(new CartLine($variant, $allowed));
        }

        if ($clean !== $items) {
            $this->store($clean);
        }

        return $lines;
    }

    /** Total number of items (for the header badge). Does not touch the database. */
    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function isEmpty(): bool
    {
        return $this->raw() === [];
    }

    /** Sum of line totals in Toman (before shipping). */
    public function subtotal(): int
    {
        return (int) $this->lines()->sum('lineTotal');
    }

    /** Total weight in grams (for shipping and the free-shipping threshold). */
    public function totalWeightGrams(): int
    {
        return (int) $this->lines()->sum('weightGrams');
    }

    /** @return array<int, int> variant_id => quantity */
    public function raw(): array
    {
        $items = $this->session->get(self::SESSION_KEY, []);

        if (! is_array($items)) {
            return [];
        }

        // Keep only positive whole numbers: variant_id => quantity.
        $clean = [];
        foreach ($items as $variantId => $quantity) {
            if (is_int($variantId) && $variantId > 0 && is_int($quantity) && $quantity > 0) {
                $clean[$variantId] = $quantity;
            }
        }

        return $clean;
    }

    /**
     * Messages about automatic corrections since the last call (each shown once).
     *
     * @return array<int, string>
     */
    public function pullAdjustments(): array
    {
        return array_values(array_unique($this->session->pull(self::ADJUSTMENTS_KEY, [])));
    }

    private function put(ProductVariant $variant, int $quantity): void
    {
        if ($quantity > self::MAX_PER_ITEM) {
            throw new CartException(__('cart.max_per_item', ['max' => Digits::toPersian(self::MAX_PER_ITEM)]));
        }

        if ($quantity > $variant->stock) {
            throw new CartException(__('cart.only_stock_left', [
                'name' => $this->label($variant),
                'stock' => Digits::toPersian($variant->stock),
            ]));
        }

        $items = $this->raw();
        $items[$variant->id] = $quantity;
        $this->store($items);
    }

    /** @throws CartException */
    private function findPurchasable(int $variantId): ProductVariant
    {
        $variant = ProductVariant::with('product')->find($variantId);

        if (! $variant || ! $this->isSellable($variant)) {
            throw new CartException(__('cart.not_available'));
        }

        return $variant;
    }

    /** Variant can be bought and its product is visible in the shop. */
    private function isSellable(ProductVariant $variant): bool
    {
        return $variant->isPurchasable() && $variant->product?->is_active === true;
    }

    /** @throws CartException */
    private function assertPositive(int $quantity): void
    {
        if ($quantity < 1) {
            throw new CartException(__('cart.invalid_quantity'));
        }
    }

    private function label(ProductVariant $variant): string
    {
        return trim(($variant->product?->name ?? '').' ('.$variant->title.')');
    }

    /** @param  array<int, int>  $items */
    private function store(array $items): void
    {
        $this->session->put(self::SESSION_KEY, $items);
    }

    private function rememberAdjustment(string $message): void
    {
        $messages = $this->session->get(self::ADJUSTMENTS_KEY, []);
        $messages[] = $message;
        $this->session->put(self::ADJUSTMENTS_KEY, $messages);
    }
}
