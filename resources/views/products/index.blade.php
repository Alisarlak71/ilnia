{{-- Product list: /products, optional ?category=slug filter. --}}
<x-layouts.app :title="$category ? $category->name : __('shop.products_title')">
    <section class="mx-auto max-w-6xl px-4 py-10 sm:py-14">
        <header class="text-center">
            <h1 class="text-3xl font-extrabold">{{ $category?->name ?? __('shop.products_title') }}</h1>
            <x-ornament class="mt-4" />
            @if ($products->total() > 0)
                <p class="mt-4 text-sm text-muted">
                    {{ __('shop.products_count', ['count' => \App\Support\Digits::toPersian($products->total())]) }}
                </p>
            @endif
        </header>

        @if ($categories->count() > 1)
            <nav aria-label="{{ __('shop.category_filter') }}" class="mt-8">
                <ul class="flex flex-wrap justify-center gap-2">
                    <li>
                        <a href="{{ route('products.index') }}"
                           @class([
                               'inline-block rounded-btn border px-4 py-2 text-sm transition-colors',
                               'border-ink bg-ink text-cream' => ! $category,
                               'border-line bg-paper hover:border-gold-deep' => $category,
                           ])
                           @if (! $category) aria-current="page" @endif>
                            {{ __('shop.all_products') }}
                        </a>
                    </li>
                    @foreach ($categories as $item)
                        @php($current = $category?->is($item))
                        <li>
                            <a href="{{ route('products.index', ['category' => $item->slug]) }}"
                               @class([
                                   'inline-block rounded-btn border px-4 py-2 text-sm transition-colors',
                                   'border-ink bg-ink text-cream' => $current,
                                   'border-line bg-paper hover:border-gold-deep' => ! $current,
                               ])
                               @if ($current) aria-current="page" @endif>
                                {{ $item->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif

        @if ($products->isEmpty())
            <div class="mx-auto mt-12 max-w-md rounded-card border border-line bg-paper p-8 text-center">
                <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="56" height="56" alt="" class="mx-auto opacity-40">
                <p class="mt-4 text-muted">{{ __('shop.products_empty') }}</p>
            </div>
        @else
            <div class="mt-10 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            {{ $products->links('partials.pagination') }}
        @endif
    </section>
</x-layouts.app>
