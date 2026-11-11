{{-- Payment result (temporary layout; designed in the "payment result pages" roadmap step). --}}
@php
    use App\Enums\OrderStatus;
    $status = $order?->status;
    $ref = $order?->payments->firstWhere('status', \App\Enums\PaymentStatus::Paid)?->ref_id;
    $text = $message ?? match (true) {
        $status === OrderStatus::NeedsAttention => __('payment.result_attention'),
        $status?->isPaid() => __('payment.result_paid'),
        $status === OrderStatus::PaymentFailed => __('payment.result_failed'),
        default => __('payment.result_pending'),
    };
@endphp

<x-layouts.app :title="__('payment.result_title')">
    <section class="mx-auto max-w-md px-4 py-16 text-center">
        <h1 class="text-2xl font-extrabold">{{ __('payment.result_title') }}</h1>
        <p class="mt-4">{{ $text }}</p>
        @if ($order)
            <p class="mt-3 text-sm text-muted">{{ __('payment.order_number', ['number' => $order->number]) }}</p>
            @if ($ref)
                <p class="mt-1 text-sm text-muted">{{ __('payment.ref_id', ['ref' => \App\Support\Digits::toPersian($ref)]) }}</p>
            @endif
        @endif
        <a href="{{ route('home') }}" class="mt-8 inline-block text-gold-deep hover:underline">{{ __('payment.back_home') }}</a>
    </section>
</x-layouts.app>
