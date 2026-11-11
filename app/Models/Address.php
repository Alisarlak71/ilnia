<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A customer's saved shipping address. Always read through $user->addresses()
 * so a customer can only reach their own addresses.
 */
#[Fillable(['recipient_name', 'mobile', 'province', 'city', 'address_line', 'postal_code'])]
class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** One-line summary, e.g. «تهران، تهران، خیابان …». */
    public function summary(): string
    {
        return $this->province.'، '.$this->city.'، '.$this->address_line;
    }
}
