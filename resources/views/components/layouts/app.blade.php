{{--
    Base layout for every storefront page.
    Usage:
        <x-layouts.app title="عنوان صفحه">
            ...page content...
        </x-layouts.app>
    Without a title, the browser tab shows «ایلنیا | به رسم نیاکان».
--}}
@props(['title' => null])

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E1A16">
    <title>{{ $title ? $title.' | '.__('site.name') : __('site.name').' | '.__('site.slogan_primary') }}</title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

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
