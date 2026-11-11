{{-- Small gilim-style divider: a line with three diamonds. Decoration only. --}}
@props(['tone' => 'deep'])

@php($color = $tone === 'light' ? 'text-gold' : 'text-gold-deep')

<div {{ $attributes->merge(['class' => "flex items-center justify-center gap-3 $color"]) }} aria-hidden="true">
    <span class="h-px w-12 bg-current opacity-50"></span>
    <svg width="44" height="10" viewBox="0 0 44 10" fill="currentColor">
        <path d="M5 0l5 5-5 5-5-5z" opacity=".6"/>
        <path d="M22 0l5 5-5 5-5-5z"/>
        <path d="M39 0l5 5-5 5-5-5z" opacity=".6"/>
    </svg>
    <span class="h-px w-12 bg-current opacity-50"></span>
</div>
