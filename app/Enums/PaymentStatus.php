<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** One payment attempt at the gateway. */
enum PaymentStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';       // sent to the gateway, waiting for callback
    case Paid = 'paid';             // verified on the server
    case Failed = 'failed';         // gateway or verify failed
    case Cancelled = 'cancelled';   // customer cancelled at the gateway

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار',
            self::Paid => 'موفق',
            self::Failed => 'ناموفق',
            self::Cancelled => 'انصراف',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Paid => 'success',
            self::Failed => 'danger',
            self::Cancelled => 'warning',
        };
    }
}
