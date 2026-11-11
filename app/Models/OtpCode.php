<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

/**
 * A one-time login code. Only the hash is stored (code_hash), never the code itself.
 * Old rows are removed by `php artisan model:prune` (scheduled daily).
 */
#[Fillable(['mobile', 'code_hash', 'expires_at', 'attempts'])]
class OtpCode extends Model
{
    use MassPrunable;

    protected $hidden = ['code_hash'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /** Remove codes that expired more than a day ago. */
    public function prunable(): Builder
    {
        return static::query()->where('expires_at', '<', now()->subDay());
    }
}
