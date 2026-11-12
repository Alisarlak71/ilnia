{{--
    Shown instead of a product photo until a real photo exists (honesty rule: no stock photos).
    A warm cream surface with a faint gilim lattice and the Ilnia mark (docs/DESIGN.md).
--}}
@props(['label' => __('shop.no_image')])

<div {{ $attributes->merge(['class' => 'relative isolate flex items-center justify-center overflow-hidden bg-gradient-to-b from-paper to-cream']) }}
     role="img" aria-label="{{ $label }}">
    <div class="bg-gilim absolute inset-0 -z-10 text-gold-deep/[0.07]" aria-hidden="true"></div>
    <img src="{{ asset('brand/logo-mark-ink.svg') }}" alt="" class="w-1/3 max-w-24 opacity-25">
</div>
