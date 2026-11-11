<?php

namespace App\Jobs;

use App\Exceptions\SmsException;
use App\Models\Order;
use App\Services\Sms\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Order confirmation SMS, sent from the queue so the payment result page never waits for it.
 * (On the host the queue runs from the scheduler: see routes/console.php.)
 */
class SendOrderConfirmationSms implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public readonly int $orderId)
    {
    }

    public function handle(SmsService $sms): void
    {
        $order = Order::with('user')->find($this->orderId);

        if (! $order) {
            return;
        }

        $mobile = $order->user?->mobile ?? $order->recipient_mobile;

        try {
            $sms->sendOrderConfirmation($mobile, $order->number);
        } catch (SmsException $e) {
            Log::warning('Order confirmation SMS failed', ['order' => $order->number, 'error' => $e->getMessage()]);
            $this->release(60);
        }
    }
}
