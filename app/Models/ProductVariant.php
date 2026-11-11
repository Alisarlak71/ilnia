<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One weight of a product (e.g. «۱ کیلوگرم»). Price (Toman, integer) and stock live here.
 */
#[Fillable(['product_id', 'title', 'weight_grams', 'price', 'stock', 'sku', 'is_active', 'sort'])]
class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    protected $attributes = [
        'stock' => 0,
        'is_active' => true,
        'sort' => 0,
    ];

    protected function casts(): array
    {
        return [
            'weight_grams' => 'integer',
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** Has a price set by the admin. */
    public function hasPrice(): bool
    {
        return $this->price !== null && $this->price > 0;
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /** Can be added to the cart: active, priced and in stock. */
    public function isPurchasable(): bool
    {
        return $this->is_active && $this->hasPrice() && $this->inStock();
    }
}
