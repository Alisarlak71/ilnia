<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// `is_admin` is intentionally NOT fillable: it can never be set from a request.
#[Fillable(['name', 'mobile', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Default values for new (not yet saved) users.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_admin' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /** Saved shipping addresses (newest first). @return HasMany<Address, $this> */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->latest('id');
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest('id');
    }

    /**
     * Only admins may enter the admin panel (/admin).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }
}
