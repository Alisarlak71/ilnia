@php
    use App\Enums\OrderStatus;
    use App\Enums\PaymentStatus;
    use App\Support\Digits;
    use App\Support\Jalali;
    use App\Support\Toman;

    $ref = $order->payments->firstWhere('status', PaymentStatus::Paid)?->ref_id;

    // Progress steps of a normal order (shown only once the money has been received).
    $steps = [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered];
    $currentStep = array_search($order->status, $steps, true);
@endphp

<x-layouts.app :title="__('account.order_title', ['number' => $order->number])">
    <section class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
        <a href="{{ route('account.orders') }}" class="text-sm text-gold-deep hover:underline">→ {{ __('account.back_to_orders') }}</a>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-extrabold">{{ __('account.order_number') }}: <span dir="ltr">{{ $order->number }}</span></h1>
            <x-order-status :status="$order->status" class="text-sm" />
        </div>
        <p class="mt-2 text-sm text-muted">{{ __('account.order_date') }}: {{ Jalali::dateTime($order->created_at) }}</p>

        {{-- Status --}}
        <div class="mt-6 rounded-card border border-line bg-paper p-5 sm:p-6">
            <p>{{ __('account.status_hint.'.$order->status->value) }}</p>

            @if ($currentStep !== false)
                <ol class="mt-5 grid grid-cols-4 gap-2 text-center text-xs">
                    @foreach ($steps as $i => $step)
                        <li @if ($i === $currentStep) aria-current="step" @endif>
                            <span @class(['block h-1.5 rounded-full', 'bg-gold-deep' => $i <= $currentStep, 'bg-line' => $i > $currentStep])></span>
                            <span @class(['mt-2 block', 'font-bold' => $i === $currentStep, 'text-muted' => $i !== $currentStep])>{{ $step->getLabel() }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif

            @if ($order->tracking_code)
                <div class="mt-6 rounded-btn border border-gold-deep/30 bg-cream p-4" x-data="{ copied: false }">
                    <p class="text-sm text-muted">{{ __('account.tracking_code') }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-3">
                        <span class="text-lg font-extrabold tracking-wider" dir="ltr">{{ $order->tracking_code }}</span>
                        <button type="button" class="rounded-btn border border-line px-3 py-1 text-xs hover:border-gold-deep"
                                x-on:click="navigator.clipboard?.writeText(@js($order->tracking_code)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                            <span x-show="! copied">{{ __('account.copy') }}</span>
                            <span x-show="copied" x-cloak>{{ __('account.copied') }}</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-muted">
                        {{ __('account.tracking_hint') }}
                        <a href="https://tracking.post.ir/" target="_blank" rel="noopener" class="text-gold-deep hover:underline">{{ __('account.tracking_link') }}</a>
                    </p>
                </div>
            @endif

            @if ($canRetry)
                <form method="POST" action="{{ route('payment.retry', $order) }}" class="mt-5" x-data="{ sending: false }" x-on:submit="sending = true">
                    @csrf
                    <button type="submit" x-bind:disabled="sending"
                            class="w-full rounded-btn border border-ink bg-ink px-7 py-3 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60 sm:w-auto">
                        {{ __('account.retry') }}
                    </button>
                </form>
            @endif
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            {{-- Items and amounts --}}
            <div class="rounded-card border border-line bg-paper p-5 sm:p-6">
                <h2 class="font-extrabold">{{ __('account.items') }}</h2>
                <ul class="mt-3 divide-y divide-line text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-3 py-2.5">
                            <span>{{ $item->product_name }} ({{ $item->variant_title }}) <span class="whitespace-nowrap">× {{ Digits::toPersian($item->quantity) }}</span></span>
                            <span class="shrink-0">{{ Toman::format($item->line_total) }}</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-3 space-y-2 border-t border-line pt-3 text-sm">
                    <div class="flex justify-between"><dt>{{ __('account.subtotal') }}</dt><dd>{{ Toman::format($order->subtotal) }}</dd></div>
                    <div class="flex justify-between">
                        <dt>{{ __('account.shipping_cost') }}</dt>
                        <dd class="{{ $order->shipping_cost === 0 ? 'font-bold text-success' : '' }}">
                            {{ $order->shipping_cost === 0 ? __('account.shipping_free') : Toman::format($order->shipping_cost) }}
                        </dd>
                    </div>
                    @if ($order->discount > 0)
                        <div class="flex justify-between"><dt>{{ __('account.discount') }}</dt><dd>{{ Toman::format($order->discount) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-line pt-3 text-base">
                        <dt class="font-bold">{{ __('account.total') }}</dt>
                        <dd class="font-extrabold text-gold-deep">{{ Toman::format($order->total) }}</dd>
                    </div>
                </dl>
                @if ($order->paid_at)
                    <p class="mt-3 text-xs text-muted">
                        {{ __('account.paid_at') }}: {{ Jalali::dateTime($order->paid_at) }}
                        @if ($ref)
                            · {{ __('account.ref_id') }}: {{ Digits::toPersian($ref) }}
                        @endif
                    </p>
                @endif
            </div>

            {{-- Delivery --}}
            <div class="rounded-card border border-line bg-paper p-5 sm:p-6">
                <h2 class="font-extrabold">{{ __('account.delivery') }}</h2>
                <dl class="mt-3 space-y-3 text-sm">
                    <div>
                        <dt class="text-muted">{{ __('account.shipping_method') }}</dt>
                        <dd class="mt-0.5">{{ $order->shipping_method_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted">{{ __('account.address') }}</dt>
                        <dd class="mt-0.5 leading-7">{{ $order->addressSummary() }}</dd>
                        <dd class="text-muted">
                            {{ $order->recipient_name }} · {{ Digits::toPersian($order->recipient_mobile) }}
                            · {{ __('account.postal_code') }}: {{ Digits::toPersian($order->postal_code) }}
                        </dd>
                    </div>
                    @if ($order->customer_note)
                        <div>
                            <dt class="text-muted">{{ __('account.note') }}</dt>
                            <dd class="mt-0.5">{{ $order->customer_note }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </section>
</x-layouts.app>
