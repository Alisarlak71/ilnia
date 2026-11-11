{{-- Temporary brand-check page. Replaced by the real layout and home page in later roadmap steps. --}}
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans">
    <header class="bg-ink px-4 py-5">
        <div class="mx-auto flex max-w-3xl items-center gap-3">
            <img src="{{ asset('brand/logo-mark-gold.svg') }}" width="44" height="44" alt="">
            <span class="font-brand text-3xl leading-none text-cream">ایلنیا</span>
        </div>
    </header>

    <main class="mx-auto max-w-3xl space-y-8 px-4 py-10">
        <section class="text-center">
            <img src="{{ asset('brand/logo-mark-ink.svg') }}" width="96" height="96" alt="" class="mx-auto">
            <p class="mt-3 font-brand text-5xl leading-none">ایلنیا</p>
            <p class="mt-2 font-latin text-xs tracking-[0.55em] text-gold-deep" dir="ltr">ILNIA</p>
            <p class="mt-4 text-muted">به رسم نیاکان</p>
        </section>

        <section class="rounded-card border border-line bg-paper p-6">
            <h1 class="text-2xl font-extrabold">اصلش را می‌فروشیم، نه ادعایش را</h1>
            <p class="mt-2 text-muted">
                این صفحهٔ موقت فقط برای بررسی فونت‌ها و رنگ‌های برند است. اعداد فارسی: ۱۲۳۴۵۶۷۸۹۰
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <span class="rounded-btn bg-ink px-5 py-2 text-cream">دکمهٔ اصلی</span>
                <span class="rounded-btn border border-gold-deep px-5 py-2">دکمهٔ ثانویه</span>
            </div>
        </section>

        <section class="grid grid-cols-3 gap-3 text-center text-sm sm:grid-cols-6">
            <div><div class="h-12 rounded-btn bg-ink"></div>ink</div>
            <div><div class="h-12 rounded-btn bg-gold"></div>gold</div>
            <div><div class="h-12 rounded-btn bg-gold-deep"></div>gold-deep</div>
            <div><div class="h-12 rounded-btn bg-honey"></div>honey</div>
            <div><div class="h-12 rounded-btn border border-line bg-cream"></div>cream</div>
            <div><div class="h-12 rounded-btn border border-line bg-paper"></div>paper</div>
        </section>
    </main>
</body>
</html>
