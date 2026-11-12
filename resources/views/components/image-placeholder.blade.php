{{--
    Shown instead of a product photo until a real photo exists (honesty rule: no stock photos).
    A flat cream surface with the Ilnia mark (docs/DESIGN.md).
--}}
@props(['label' => __('shop.no_image')])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center bg-cream']) }}
     role="img" aria-label="{{ $label }}">
    <img src="{{ asset('brand/logo-mark-ink.svg') }}" alt="" class="w-1/3 max-w-24 opacity-25">
</div>
