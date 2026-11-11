{{-- Order status badge for customers. --}}
@props(['status'])

@php
    $tone = match ($status) {
        \App\Enums\OrderStatus::Delivered => 'border-success/30 bg-success/10 text-success',
        \App\Enums\OrderStatus::Paid, \App\Enums\OrderStatus::Processing, \App\Enums\OrderStatus::Shipped => 'border-gold-deep/30 bg-honey/10 text-gold-deep',
        \App\Enums\OrderStatus::PaymentFailed, \App\Enums\OrderStatus::Cancelled => 'border-danger/30 bg-danger/10 text-danger',
        \App\Enums\OrderStatus::NeedsAttention => 'border-honey/40 bg-honey/15 text-ink',
        default => 'border-line bg-cream text-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-block whitespace-nowrap rounded-full border px-3 py-0.5 text-xs font-bold $tone"]) }}>{{ $status->getLabel() }}</span>
