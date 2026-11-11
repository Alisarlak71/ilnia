<?php

namespace App\Models;

use Database\Factories\ShippingMethodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Shipping option (for now only «پست»). Cost and free-shipping threshold are set in the panel.
 */
#[Fillable(['name', 'cost', 'free_over_weight_grams', 'is_active', 'sort'])]
class ShippingMethod extends Model
{
    /** @use HasFactory<ShippingMethodFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'cost' => 'integer',
            'free_over_weight_grams' => 'integer',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort')->orderBy('id');
    }
}
