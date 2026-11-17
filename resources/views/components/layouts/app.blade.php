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
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
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
    {{-- Declared here (not in app.css) so the URLs are on this site's own address, also under `npm run dev`
         where app.css comes from the Vite server. Files: public/fonts (self-hosted, OFL). --}}
    <style>
        @font-face { font-family: 'Vazirmatn'; font-style: normal; font-weight: 100 900; font-display: swap; src: url('{{ asset('fonts/vazirmatn-variable.woff2') }}') format('woff2'); }
        @font-face { font-family: 'Reem Kufi'; font-style: normal; font-weight: 600; font-display: swap; src: url('{{ asset('fonts/reem-kufi-600-arabic.woff2') }}') format('woff2'); }
        @font-face { font-family: 'Cinzel'; font-style: normal; font-weight: 600; font-display: swap; src: url('{{ asset('fonts/cinzel-600-latin.woff2') }}') format('woff2'); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
{{-- Bottom padding on phones = height of the bottom bar (h-16) + the iPhone home indicator. --}}
<body class="flex min-h-screen flex-col bg-cream pb-[calc(4rem+env(safe-area-inset-bottom))] font-sans text-ink antialiased md:pb-0">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded-btn focus:bg-paper focus:px-4 focus:py-2 focus:text-ink">
        {{ __('site.skip_to_content') }}
    </a>

    <x-site.header />

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer />

    <x-site.bottom-nav />

    <x-site.support-button />
</body>
</html>
