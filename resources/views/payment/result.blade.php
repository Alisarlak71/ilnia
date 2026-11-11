{{--
    Payment result: paid / needs attention / failed / not paid yet / closed (cancelled),
    or an error with no order (unknown payment, verify unavailable).
    Variables: $order (?Order, with items + payments), $message (?string), $canRetry (bool).
--}}
@php
    use App\Enums\OrderStatus;
    use App\Enums\PaymentStatus;
    use App\Support\Digits;
    use App\Support\Jalali;
    use App\Support\Toman;

    $status = $order?->status;

    $state = match (true) {
        $order === null => 'error',
        $status === OrderStatus::NeedsAttention => 'attention',
        $status->isPaid() => 'paid',
        $status === OrderStatus::PaymentFailed => 'failed',
        $status === OrderStatus::PendingPayment => 'pending',
        default => 'closed',
    };

    $title = __('payment.'.$state.'_title');
    $text = match ($state) {
        'paid' => __('payment.result_paid'),
        'attention' => __('payment.result_attention'),
        'failed' => __('payment.result_failed'),
        'pending' => __('payment.result_pending'),
        default => null,
    };

    $ref = $order?->payments->firstWhere('status', PaymentStatus::Paid)?->ref_id;
    $showDetails = in_array($state, ['paid', 'attention'], true);

    // Icon circle colours per state.
    $tone = match ($state) {
        'paid' => 'bg-success/10 text-success ring-success/25',
        'attention' => 'bg-honey/15 text-gold-deep ring-honey/35',
        'failed', 'error' => 'bg-danger/10 text-danger ring-danger/25',
        default => 'bg-ink/5 text-muted ring-line',
    };
@endphp

<x-layouts.app :title="$title">
    <section class="mx-auto max-w-2xl px-4 py-10 sm:py-14">
        {{-- Status --}}
        <div class="rounded-card border border-line bg-paper px-5 py-8 text-center sm:px-10">
            <span class="mx-auto flex size-16 items-center justify-center rounded-full ring-1 {{ $tone }}" aria-hidden="true">
                @switch($state)
                    @case('paid')
                        <svg viewBox="0 0 24 24" class="size-8" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                        @break
                    @case('failed')
                    @case('error')
                        <svg viewBox="0 0 24 24" class="size-8" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M7 7l10 10M17 7L7 17"/></svg>
                        @break
                    @case('attention')
                        <svg viewBox="0 0 24 24" class="size-8" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 6.5v7"/><circle cx="12" cy="17.5" r="0.6" fill="currentColor"/></svg>
                        @break
                    @default
                        <svg viewBox="0 0 24 24" class="size-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l3 2"/></svg>
                @endswitch
            </span>

            <h1 class="mt-5 text-2xl font-extrabold">{{ $title }}</h1>

            @if ($text)
                <p class="mx-auto mt-3 max-w-md leading-8 text-muted">{{ $text }}</p>
            @endif

            @if ($message)
                <p role="alert" class="mx-auto mt-5 max-w-md rounded-btn border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">{{ $message }}</p>
            @endif

            @if ($order)
                <dl class="mx-auto mt-7 grid max-w-md grid-cols-2 gap-px overflow-hidden rounded-btn border border-line bg-line text-sm">
                    <div class="bg-cream px-4 py-3">
                        <dt class="text-muted">{{ __('payment.order_number') }}</dt>
                        <dd class="mt-1 font-bold" dir="ltr">{{ $order->number }}</dd>
                    </div>
                    <div class="bg-cream px-4 py-3">
                        <dt class="text-muted">{{ __('payment.amount') }}</dt>
                        <dd class="mt-1 font-bold">{{ Toman::format($order->total) }}</dd>
                    </div>
                    <div class="bg-cream px-4 py-3">
                        <dt class="text-muted">{{ __('payment.order_date') }}</dt>
                        <dd class="mt-1 font-bold">{{ Jalali::date($order->created_at) }}</dd>
                    </div>
                    <div class="bg-cream px-4 py-3">
                        @if ($ref)
                            <dt class="text-muted">{{ __('payment.ref_id') }}</dt>
                            <dd class="mt-1 font-bold">{{ Digits::toPersian($ref) }}</dd>
                        @else
                            <dt class="text-muted">{{ __('payment.order_status') }}</dt>
                            <dd class="mt-1 font-bold">{{ $status->getLabel() }}</dd>
                        @endif
                    </div>
                </dl>

                @if ($ref)
                    <p class="mt-3 text-xs text-muted">{{ __('payment.keep_ref') }}</p>
                @endif
            @endif

            {{-- Actions --}}
            <div class="mt-8 mx-auto flex max-w-xs flex-col items-center justify-center gap-3 sm:max-w-none sm:flex-row">
                @if ($canRetry)
                    <form class="w-full sm:w-auto" method="POST" action="{{ route('payment.retry', $order) }}" x-data="{ sending: false }" @submit="sending = true">
                        @csrf
                        <button type="submit" :disabled="sending"
                                class="w-full rounded-btn border border-ink bg-ink px-7 py-3 font-bold text-cream transition-colors hover:border-gold disabled:opacity-60 sm:w-auto">
                            {{ __('payment.retry') }}
                        </button>
                    </form>
                    <a href="{{ route('home') }}" class="w-full rounded-btn border border-gold-deep px-7 py-3 hover:bg-cream sm:w-auto">{{ __('payment.back_home') }}</a>
                @else
                    <a href="{{ route('products.index') }}"
                       class="w-full rounded-btn border border-ink bg-ink px-7 py-3 font-bold text-cream transition-colors hover:border-gold sm:w-auto">
                        {{ __('payment.continue_shopping') }}
                    </a>
                    <a href="{{ route('home') }}" class="w-full rounded-btn border border-gold-deep px-7 py-3 hover:bg-cream sm:w-auto">{{ __('payment.back_home') }}</a>
                @endif
            </div>
            @if ($canRetry)
                <p class="mt-3 text-xs text-muted">{{ __('payment.retry_hint') }}</p>
            @endif
        </div>

        {{-- Order details (after a payment) --}}
        @if ($showDetails)
            <div class="mt-6 rounded-card border border-line bg-paper p-5 sm:p-6">
                <h2 class="font-extrabold">{{ __('payment.items') }}</h2>
                <ul class="mt-3 divide-y divide-line text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-3 py-2.5">
                            <span>{{ $item->product_name }} ({{ $item->variant_title }}) × {{ Digits::toPersian($item->quantity) }}</span>
                            <span class="shrink-0">{{ Toman::format($item->line_total) }}</span>
                        </li>
                    @endforeach
                    <li class="flex justify-between gap-3 py-2.5">
                        <span>{{ __('payment.shipping') }} ({{ $order->shipping_method_name }})</span>
                        <span class="shrink-0 {{ $order->shipping_cost === 0 ? 'font-bold text-success' : '' }}">
                            {{ $order->shipping_cost === 0 ? __('payment.shipping_free') : Toman::format($order->shipping_cost) }}
                        </span>
                    </li>
                </ul>

                <h2 class="mt-6 font-extrabold">{{ __('payment.address') }}</h2>
                <p class="mt-2 text-sm leading-7">{{ $order->addressSummary() }}</p>
                <p class="text-sm text-muted">{{ $order->recipient_name }} · {{ Digits::toPersian($order->recipient_mobile) }}</p>
            </div>
        @endif
    </section>
</x-layouts.app>
