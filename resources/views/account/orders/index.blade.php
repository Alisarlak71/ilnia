@php
    use App\Support\Digits;
    use App\Support\Jalali;
    use App\Support\Toman;
@endphp

<x-layouts.app :title="__('account.orders_title')">
    <section class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
        <h1 class="text-center text-3xl font-extrabold">{{ __('account.orders_title') }}</h1>
        <x-ornament class="mt-4" />
        <x-account-tabs class="mt-8" />

        @if ($orders->isEmpty())
            <div class="mx-auto mt-10 max-w-md rounded-card border border-line bg-paper p-8 text-center">
                <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="56" height="56" alt="" class="mx-auto opacity-40">
                <h2 class="mt-4 text-lg font-bold">{{ __('account.orders_empty_title') }}</h2>
                <p class="mt-2 text-muted">{{ __('account.orders_empty_text') }}</p>
                <a href="{{ route('products.index') }}"
                   class="mt-6 inline-block rounded-btn border border-ink bg-ink px-6 py-3 font-bold text-cream transition-colors hover:border-gold">
                    {{ __('account.browse_products') }}
                </a>
            </div>
        @else
            <ul class="mt-8 space-y-3">
                @foreach ($orders as $order)
                    <li>
                        <a href="{{ route('account.orders.show', $order->number) }}"
                           class="group flex flex-wrap items-center justify-between gap-x-6 gap-y-3 rounded-card border border-line bg-paper p-4 transition-colors hover:border-gold-deep/60 sm:p-5">
                            <div class="space-y-1">
                                <p class="font-bold" dir="ltr">{{ $order->number }}</p>
                                <p class="text-sm text-muted">
                                    {{ Jalali::date($order->created_at) }} · {{ __('account.items_count', ['count' => Digits::toPersian($order->items_count)]) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <x-order-status :status="$order->status" />
                                <span class="whitespace-nowrap font-bold">{{ Toman::format($order->total) }}</span>
                                <span class="hidden text-sm text-gold-deep group-hover:underline sm:inline" aria-hidden="true">{{ __('account.view_order') }} ←</span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($orders->hasPages())
                <div class="mt-8 flex justify-between text-sm">
                    @if ($orders->previousPageUrl())
                        <a href="{{ $orders->previousPageUrl() }}" class="rounded-btn border border-line px-4 py-2 hover:border-gold-deep">→ {{ __('shop.previous') }}</a>
                    @else
                        <span></span>
                    @endif
                    @if ($orders->nextPageUrl())
                        <a href="{{ $orders->nextPageUrl() }}" class="rounded-btn border border-line px-4 py-2 hover:border-gold-deep">{{ __('shop.next') }} ←</a>
                    @endif
                </div>
            @endif
        @endif
    </section>
</x-layouts.app>
