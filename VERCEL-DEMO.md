# شاخهٔ vercel-demo (فقط برای نمایش)

این شاخه فقط برای نشان دادن سایت روی Vercel است. **سایت واقعی روی هاست اشتراکی اجرا می‌شود و این شاخه هرگز در `main` ادغام (merge) نمی‌شود.**

## تفاوت‌ها با main
- `api/index.php` + `vercel.json`: لاراول را با runtime غیررسمی `vercel-php@0.9.0` (PHP 8.5) اجرا می‌کنند. این runtime از ۲۹ اکتبر ۲۰۲۶ بایگانی شده است.
- `database/demo.sqlite`: کپی پاک‌شدهٔ دیتابیس محلی، فقط با محصولات و صفحه‌ها و **بدون کاربر، آدرس و سفارش**. با هر بار بالا آمدن instance جدید، به `/tmp` کپی می‌شود.
- فایل‌های ساخته‌شده (`public/build`، فایل‌های Filament و `public/storage/products`) به‌زور در Git ثبت شده‌اند، چون Vercel آن‌ها را نمی‌سازد.

## محدودیت‌ها
- هر تغییری در سایت (سبد خرید، ورود، سفارش) ممکن است هر لحظه پاک شود.
- ورود با `SMS_DRIVER=screen` است، پس کد ورود روی صفحه نمایش داده می‌شود و با هر شماره‌ای می‌شود وارد شد. فقط برای دمو مناسب است.
- پنل مدیریت کاربر ادمین ندارد.

## به‌روزرسانی دمو با آخرین تغییرات main
```bash
git merge main
npm run build            # در پوشهٔ اصلی پروژه؛ بعد public/build را اینجا کپی کن
git add -f public/build
git commit -m "Update demo" && git push
```
متغیرهای محیطی روی پروژهٔ Vercel تنظیم شده‌اند (Preview، شاخهٔ `vercel-demo`).
- `PUBLIC_STORAGE_PATH=/var/task/user/public/storage`: لازم است، چون فایل‌سیستم Vercel فقط‌خواندنی است و لاراول در غیر این صورت سعی می‌کند پوشهٔ `storage/app/public` را بسازد و خطای ۵۰۰ می‌دهد.

لینک ثابت دمو: https://ilnia-git-vercel-demo-ali-sarlaks-projects.vercel.app
