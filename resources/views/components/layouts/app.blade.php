{{--
    Base layout for every storefront page.
    Usage:
        <x-layouts.app title="عنوان صفحه">
            ...page content...
        </x-layouts.app>
    Without a title, the browser tab shows «ایلنیا | به رسم نیاکان».
--}}
@props([
    'title' => null,
    'description' => null,
    'image' => null,       // absolute URL for the link preview; default: brand mark
    'imageAlt' => null,
    'type' => 'website',   // og:type (product pages: 'product')
    'canonical' => null,   // default: current URL without query string
    'noindex' => false,
])

@php
    $siteName = __('site.name');
    $pageTitle = $title ? $title.' | '.$siteName : $siteName.' | '.__('site.slogan_primary');
    $metaDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($description ?: __('site.meta_description'))))), 160, '…');
    $ogImage = $image ?: asset('brand/profile-mark.png');

    // Private pages are never indexed (cart, checkout, login, account, payment results).
    $noindex = $noindex || request()->routeIs('cart', 'checkout', 'login', 'account.*', 'payment.*');
    $canonicalUrl = $canonical ?: url()->current();
@endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E1A16">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif

    {{-- Link previews (Telegram, WhatsApp, Instagram DMs…) --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:title" content="{{ $title ?: $siteName.' | '.__('site.slogan_primary') }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $imageAlt ?: $siteName }}">
    <meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    {{-- Fonts first, so text never flashes in a fallback face (slow connections, tunnels). --}}
    <link rel="preload" href="{{ asset('fonts/vazirmatn-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/reem-kufi-600-arabic.woff2') }}" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="flex min-h-screen flex-col bg-cream font-sans text-ink antialiased">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded-btn focus:bg-paper focus:px-4 focus:py-2 focus:text-ink">
        {{ __('site.skip_to_content') }}
    </a>

    <x-site.header />

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer />
</body>
</html>
