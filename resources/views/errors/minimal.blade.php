{{--
    Standalone page for server errors (500, 503). It must work even when the database, session,
    Vite build or Livewire are broken, so it uses NO layout, NO database and only inline CSS.
    The only external files are the static font and logo in public/.
--}}
@php
    $toPersian = fn ($value) => strtr((string) $value, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#1E1A16">
    <title>{{ $title }} | ایلنیا</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        @font-face { font-family: 'Vazirmatn'; font-weight: 100 900; font-display: swap; src: url('/fonts/vazirmatn-variable.woff2') format('woff2'); }
        @font-face { font-family: 'Reem Kufi'; font-weight: 600; font-display: swap; src: url('/fonts/reem-kufi-600-arabic.woff2') format('woff2'); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; flex-direction: column; background: #F6EFE2; color: #1E1A16;
               font-family: 'Vazirmatn', Tahoma, sans-serif; line-height: 1.9; -webkit-font-smoothing: antialiased; }
        header { background: #1E1A16; padding: 16px; display: flex; justify-content: center; }
        header a { display: inline-flex; align-items: center; gap: 12px; color: #F6EFE2; text-decoration: none; }
        header span { font-family: 'Reem Kufi', 'Vazirmatn', sans-serif; font-size: 30px; line-height: 1; }
        .line { height: 1px; background: linear-gradient(to left, transparent, rgba(226, 180, 90, .5), transparent); }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 48px 16px; }
        .box { max-width: 520px; text-align: center; }
        .code { margin: 0; font-size: 72px; font-weight: 800; line-height: 1; letter-spacing: .05em; color: #A8731B; }
        h1 { margin: 24px 0 0; font-size: 24px; font-weight: 800; }
        p.text { margin: 16px auto 0; max-width: 440px; color: #6B6257; line-height: 2; }
        .btn { display: inline-block; margin-top: 32px; padding: 12px 28px; border-radius: 10px; border: 1px solid #1E1A16;
               background: #1E1A16; color: #F6EFE2; font-weight: 700; text-decoration: none; }
        .btn:hover { border-color: #E2B45A; }
        footer { background: #1E1A16; color: #E2B45A; text-align: center; padding: 20px 16px; font-size: 14px; }
    </style>
</head>
<body>
    <header>
        <a href="/" aria-label="ایلنیا، صفحهٔ اصلی">
            <img src="/brand/logo-mark-gold.svg" width="40" height="40" alt="">
            <span>ایلنیا</span>
        </a>
    </header>
    <div class="line"></div>

    <main>
        <div class="box">
            <p class="code" aria-hidden="true">{{ $toPersian($code) }}</p>
            <h1>{{ $title }}</h1>
            <p class="text">{{ $text }}</p>
            <a class="btn" href="/">{{ $action }}</a>
        </div>
    </main>

    <footer>به رسم نیاکان</footer>
</body>
</html>
