<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One line of an order (snapshot of name, weight and price at order time). */
#[Fillable(['order_id', 'product_variant_id', 'product_name', 'variant_title', 'weight_grams', 'unit_price', 'quantity', 'line_total'])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'weight_grams' => 'integer',
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'line_total' => 'integer',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** May be null if the variant was deleted later. @return BelongsTo<ProductVariant, $this> */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
