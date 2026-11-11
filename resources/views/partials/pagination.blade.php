{{-- Simple Persian pagination: «قبلی · صفحهٔ ۱ از ۳ · بعدی». Used as $paginator->links('partials.pagination'). --}}
@if ($paginator->hasPages())
    @php($base = 'rounded-btn border px-4 py-2 text-sm')
    <nav aria-label="{{ __('shop.pagination') }}" class="mt-10 flex items-center justify-center gap-3">
        @if ($paginator->onFirstPage())
            <span class="{{ $base }} border-line text-muted/60" aria-disabled="true">{{ __('shop.previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $base }} border-line bg-paper hover:border-gold-deep">{{ __('shop.previous') }}</a>
        @endif

        <span class="text-sm text-muted">
            {{ __('shop.page_of', [
                'current' => \App\Support\Digits::toPersian($paginator->currentPage()),
                'last' => \App\Support\Digits::toPersian($paginator->lastPage()),
            ]) }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $base }} border-line bg-paper hover:border-gold-deep">{{ __('shop.next') }}</a>
        @else
            <span class="{{ $base }} border-line text-muted/60" aria-disabled="true">{{ __('shop.next') }}</span>
        @endif
    </nav>
@endif
