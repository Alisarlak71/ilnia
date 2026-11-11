# دارایی‌های برند ایلنیا

همهٔ فایل‌های هویت برند برای سایت اینجا هستند. **از این‌ها استفاده کن و لوگو یا رنگ تازه نساز.** برای دیدن همه با هم، `preview.png` یا `preview.html` را باز کن.

| فایل | کاربرد |
|---|---|
| `logo-mark-gold.svg` | نشان روی **زمینهٔ تیره** (هدر و فوتر) |
| `logo-mark-ink.svg` | نشان روی **زمینهٔ روشن** (کرم یا سفید) |
| `favicon.svg` | فاوآیکن ساده‌شده، با پس‌زمینهٔ تیره و گوشه‌های گرد |
| `profile-mark.png` | نشان دایره‌ای ۱۰۸۰×۱۰۸۰، برای `apple-touch-icon` و تصویر پیش‌فرض Open Graph |
| `profile-full.png` | همان نشان، همراه اسم |
| `tokens.css` | رنگ‌ها و فونت‌ها برای Tailwind v4 (`@theme`) |
| `brand.json` | نام، شعارها، رنگ‌ها و مسیر فایل‌ها، به شکلی که کد بتواند بخواند |
| `preview.html` / `preview.png` | برگهٔ برند برای نگاه کردن |

## اسم
- فارسی: **ایلنیا**، سرهم و بدون نیم‌فاصله.
- لاتین: **Ilnia**؛ در لوگو با حروف بزرگ و فاصلهٔ زیاد: **ILNIA**.
- `Eilnia` غلط است.

## لوگوی کامل در سایت
لوگو = نشان SVG + کلمهٔ «ایلنیا» به‌صورت **متن واقعی** با فونت Reem Kufi. اسم را به تصویر تبدیل نکن تا تیز بماند و قابل جست‌وجو باشد. پیشنهاد: یک کامپوننت Blade به اسم `<x-logo />`:

```blade
{{-- resources/views/components/logo.blade.php --}}
@props(['variant' => 'dark', 'size' => 44])
<a href="{{ route('home') }}" class="inline-flex items-center gap-3" aria-label="ایلنیا، صفحهٔ اصلی">
    <img src="{{ asset($variant === 'dark' ? 'brand/logo-mark-gold.svg' : 'brand/logo-mark-ink.svg') }}"
         width="{{ $size }}" height="{{ $size }}" alt="">
    <span class="font-brand text-3xl leading-none {{ $variant === 'dark' ? 'text-cream' : 'text-ink' }}">ایلنیا</span>
</a>
```
`variant="dark"` یعنی لوگو روی زمینهٔ تیره قرار می‌گیرد.

## انتقال به پروژهٔ لاراول (در فاز ۰)
- فایل‌های `*.svg` و `profile-mark.png` را به `public/brand/` کپی کن.
- `favicon.svg` را به `public/` کپی کن و در `<head>` بنویس:
  `<link rel="icon" href="/favicon.svg" type="image/svg+xml">`
- برای `apple-touch-icon`، فایل `profile-mark.png` را در اندازهٔ 180×180 استفاده کن.
- `tokens.css` را بعد از `@import "tailwindcss";` در `resources/css/app.css` ایمپورت کن.

## فونت‌ها
روی خود سایت میزبانی می‌شوند، نه از Google Fonts:
- **Vazirmatn:** پکیج npm به نام `vazirmatn`، یا فایل‌های woff2 از مخزن رسمی‌اش.
- **Reem Kufi** و **Cinzel:** فایل‌های woff2 را دانلود کن و در `public/fonts/` بگذار. هر دو مجوز OFL دارند و استفادهٔ تجاری‌شان آزاد است.
- Reem Kufi **فقط** برای کلمهٔ «ایلنیا» است. در تیترها حرف «ه» در ابتدای کلمه شبیه «م» خوانده می‌شود.

## قواعد رنگ
- طلایی روشن (`gold`) را **فقط روی زمینهٔ تیره** استفاده کن. روی زمینهٔ کرم، تضادش برای خواندن کافی نیست؛ آنجا از `gold-deep` استفاده کن.
- پس‌زمینهٔ صفحه‌ها کرم است و هدر و فوتر تیره.
