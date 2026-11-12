{{-- Content page (about / contact / shipping / terms). Body: Markdown from the panel. --}}
@php
    use App\Support\Toman;
    use App\Support\Weight;
@endphp

<x-layouts.app :title="$page->title" :description="$page->meta_description">
    <article class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
        @unless ($page->is_published)
            <p role="status" class="mb-8 rounded-btn border border-honey/40 bg-honey/10 px-4 py-3 text-sm">
                {{ __('site.page_draft') }}
            </p>
        @endunless

        <h1 class="text-center text-3xl font-extrabold">{{ $page->title }}</h1>
        <x-ornament class="mt-4" />

        @if ($shippingMethod)
            {{-- Live values from the panel, always equal to what the cart charges. --}}
            <div class="mt-10 grid gap-px overflow-hidden rounded-card border border-line bg-line sm:grid-cols-2">
                <div class="bg-paper p-5 text-center">
                    <p class="text-sm text-muted">{{ __('site.shipping_cost_label', ['method' => $shippingMethod->name]) }}</p>
                    <p class="mt-1 text-xl font-extrabold">{{ Toman::format($shippingMethod->cost) }}</p>
                </div>
                <div class="bg-paper p-5 text-center">
                    <p class="text-sm text-muted">{{ __('site.free_shipping_label') }}</p>
                    <p class="mt-1 text-lg font-extrabold text-success">
                        @if ($shippingMethod->free_over_weight_grams)
                            {{ __('site.free_shipping_from', ['weight' => Weight::kg($shippingMethod->free_over_weight_grams)]) }}
                        @else
                            {{ __('site.free_shipping_none') }}
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <div class="page-content mt-10 rounded-card border border-line bg-paper p-6 sm:p-10">
            {{ $page->bodyHtml() }}
        </div>
    </article>
</x-layouts.app>
