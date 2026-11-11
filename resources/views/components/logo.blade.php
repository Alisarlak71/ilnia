{{--
    Ilnia logo: mark (SVG) + the word «ایلنیا» as real text in Reem Kufi.
    Usage:  <x-logo />                     on dark backgrounds (header, footer)
            <x-logo variant="light" />     on light backgrounds (cream, white)
            <x-logo :size="32" />          smaller mark
    Source: brand/README.md — do not redraw the mark.
--}}
@props(['variant' => 'dark', 'size' => 44])

@php($onDark = $variant === 'dark')

<a href="{{ route('home') }}"
   {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}
   aria-label="ایلنیا، صفحهٔ اصلی">
    <img src="{{ asset($onDark ? 'brand/logo-mark-gold.svg' : 'brand/logo-mark-ink.svg') }}"
         width="{{ $size }}" height="{{ $size }}" alt="">
    <span class="font-brand text-3xl leading-none {{ $onDark ? 'text-cream' : 'text-ink' }}">ایلنیا</span>
</a>
