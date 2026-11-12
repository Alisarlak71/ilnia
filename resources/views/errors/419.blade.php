{{-- CSRF token expired (the page was open too long). Offer to reload the previous page. --}}
<x-error-page code="419" :title="__('errors.t419')" :text="__('errors.m419')">
    <a href="{{ url()->previous() }}"
       class="rounded-btn border border-ink bg-ink px-7 py-3 font-bold text-cream transition-colors hover:border-gold">{{ __('errors.back') }}</a>
    <a href="{{ route('home') }}" class="rounded-btn border border-gold-deep px-7 py-3 hover:bg-paper">{{ __('errors.home') }}</a>
</x-error-page>
