<?php

namespace App\Models;

use App\Models\Concerns\HasPersianSlug;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'slug', 'short_description', 'description', 'story', 'storage_instructions', 'is_active'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasPersianSlug;

    protected $attributes = [
        'is_active' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Delete images one by one so their files are removed from disk too
        // (the database cascade alone would leave the files behind).
        static::deleting(function (Product $product) {
            $product->images()->get()->each->delete();
        });
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort')->orderBy('id');
    }

    /** Each weight is one variant. @return HasMany<ProductVariant, $this> */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort')->orderBy('id');
    }

    /** Only products the admin has turned on are shown in the shop. */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Lowest price among active variants that have a price, for «از … تومان».
     * Null when no variant has a price yet.
     */
    public function startingPrice(): ?int
    {
        $price = $this->variants
            ->filter(fn (ProductVariant $variant) => $variant->is_active && $variant->price !== null)
            ->min('price');

        return $price === null ? null : (int) $price;
    }

    public function mainImage(): ?ProductImage
    {
        return $this->images->first();
    }
}
