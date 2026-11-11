<?php

namespace App\Models;

use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['product_id', 'path', 'alt', 'sort'])]
class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Remove the file from disk when the image row is deleted or its file is replaced.
        static::deleted(function (ProductImage $image) {
            Storage::disk('public')->delete($image->path);
        });

        static::updated(function (ProductImage $image) {
            if ($image->wasChanged('path') && filled($image->getOriginal('path'))) {
                Storage::disk('public')->delete($image->getOriginal('path'));
            }
        });
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    /** Persian alt text; falls back to the product name. */
    public function altText(): string
    {
        return filled($this->alt) ? $this->alt : (string) $this->product?->name;
    }
}
