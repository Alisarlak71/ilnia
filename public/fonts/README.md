# فونت‌های میزبانی‌شده روی سایت

| فایل | فونت | منبع | مجوز |
|---|---|---|---|
| `reem-kufi-600-arabic.woff2` | Reem Kufi 600، زیرمجموعهٔ عربی/فارسی | Google Fonts (fonts.gstatic.com، v28) | SIL Open Font License 1.1 |
| `cinzel-600-latin.woff2` | Cinzel 600، زیرمجموعهٔ لاتین | Google Fonts (fonts.gstatic.com، v26) | SIL Open Font License 1.1 |

- Reem Kufi **فقط** برای کلمهٔ «ایلنیا» و Cinzel **فقط** برای ILNIA استفاده می‌شوند.
- Vazirmatn اینجا نیست؛ از پکیج npm `vazirmatn` می‌آید و Vite آن را در `public/build` می‌گذارد.

## پنل مدیریت (`/admin`)
- `admin.css` فونت‌های پنل Filament را تعریف می‌کند و `AdminPanelProvider` آن را بارگذاری می‌کند.
- `vazirmatn-variable.woff2` کپی همان فایل پکیج npm `vazirmatn` (`fonts/webfonts/Vazirmatn[wght].woff2`) است. پنل Filament از Vite سایت استفاده نمی‌کند، برای همین یک نسخه اینجا لازم است. اگر پکیج `vazirmatn` به‌روز شد، این فایل را هم دوباره کپی کن.
