<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\Digits;
use App\Support\Jalali;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A customer order. Amounts are integer Toman: total = subtotal + shipping_cost - discount.
 * Address, product names and prices are copied (snapshot) at order time.
 */
#[Fillable([
    'user_id', 'status', 'subtotal', 'shipping_cost', 'discount', 'total', 'total_weight_grams',
    'shipping_method_name', 'recipient_name', 'recipient_mobile', 'province', 'city', 'address_line',
    'postal_code', 'tracking_code', 'customer_note', 'paid_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'pending_payment',
        'discount' => 0,
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'discount' => 'integer',
            'total' => 'integer',
            'total_weight_grams' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (blank($order->number)) {
                $order->number = static::generateNumber();
            }
        });
    }

    /** e.g. "IL-050821-4831": Jalali date (yyMMdd) + 4 random digits; unique. */
    public static function generateNumber(): string
    {
        $date = Digits::toEnglish(Jalali::format(now(), 'yyMMdd'));

        do {
            $number = 'IL-'.$date.'-'.random_int(1000, 9999);
        } while (static::where('number', $number)->exists());

        return $number;
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('id');
    }

    /** Full shipping address in one line (snapshot). */
    public function addressSummary(): string
    {
        return $this->province.'، '.$this->city.'، '.$this->address_line;
    }
}
