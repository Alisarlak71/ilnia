<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Order lifecycle (docs/ARCHITECTURE.md):
 *   pending_payment → paid → processing → shipped → delivered
 *   side states: payment_failed, cancelled, needs_attention
 */
enum OrderStatus: string implements HasColor, HasLabel
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case PaymentFailed = 'payment_failed';
    case Cancelled = 'cancelled';
    case NeedsAttention = 'needs_attention';

    public function getLabel(): string
    {
        return match ($this) {
            self::PendingPayment => 'در انتظار پرداخت',
            self::Paid => 'پرداخت‌شده',
            self::Processing => 'در حال آماده‌سازی',
            self::Shipped => 'ارسال‌شده',
            self::Delivered => 'تحویل‌شده',
            self::PaymentFailed => 'پرداخت ناموفق',
            self::Cancelled => 'لغوشده',
            self::NeedsAttention => 'نیاز به بررسی',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PendingPayment => 'gray',
            self::Paid, self::Processing => 'info',
            self::Shipped => 'primary',
            self::Delivered => 'success',
            self::PaymentFailed, self::Cancelled => 'danger',
            self::NeedsAttention => 'warning',
        };
    }

    /**
     * Statuses the admin may move an order to by hand. Payment states (paid, payment_failed)
     * are set only by the payment flow, never from the panel.
     *
     * @return array<int, self>
     */
    public function adminTransitions(): array
    {
        return match ($this) {
            self::PendingPayment, self::PaymentFailed => [self::Cancelled],
            self::Paid => [self::Processing, self::Shipped, self::NeedsAttention, self::Cancelled],
            self::Processing => [self::Shipped, self::NeedsAttention, self::Cancelled],
            self::Shipped => [self::Delivered, self::NeedsAttention],
            self::NeedsAttention => [self::Processing, self::Shipped, self::Cancelled],
            self::Delivered, self::Cancelled => [],
        };
    }

    /** Has the customer's money been received for this order? */
    public function isPaid(): bool
    {
        return in_array($this, [self::Paid, self::Processing, self::Shipped, self::Delivered], true);
    }
}
