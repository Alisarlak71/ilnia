# راهنمای استقرار ایلنیا روی هاست

این راهنما برای **هاست اشتراکی لینوکسی با SSH، Composer و کرون جاب** است (فرض پروژه در `AGENTS.md`). دستورها را به ترتیب اجرا کن و بعد از هر بخش نتیجه را چک کن.

در مثال‌ها نام کاربری هاست `USER` و دامنه `ilnia.ir` است. **هر دو را با مقدار واقعی عوض کن.**

---

## ۰. قبل از خرید یا تحویل هاست، این‌ها را بپرس

| مورد | لازم | چرا |
|---|---|---|
| نسخهٔ PHP | **8.4.1 یا بالاتر** (بهتر 8.5) | وابستگی‌های فعلی (Symfony) دست‌کم 8.4.1 می‌خواهند. اگر هاست قدیمی‌تر است، بخش «PHP قدیمی‌تر» را ببین. |
| افزونه‌های PHP | `intl`، `zip`، `pdo_mysql`، `mbstring`، `bcmath`، `fileinfo`، `openssl`، `curl` | Filament بدون `intl` اجرا نمی‌شود؛ `curl` برای زرین‌پال و کاوه‌نگار لازم است. |
| دیتابیس | MySQL 8 یا MariaDB 10.6 به بالا | |
| SSH و Composer | دارد | برای `composer install` و `php artisan` |
| کرون جاب | هر دقیقه | صف پیامک، لغو سفارش‌های قدیمی و پاک‌سازی کدهای ورود |
| SSL | گواهی رایگان (Let's Encrypt) | HTTPS اجباری است. |
| ریشهٔ وب | امکان تنظیم روی پوشهٔ `public/` | اگر نشد، روش «ب» در بخش ۳ |
| symlink | مجاز باشد | برای `storage:link`. اگر نبود، بخش ۶ |

روی هاست با این دستورها چک کن:
```bash
php -v
php -m | grep -iE "intl|zip|pdo_mysql|mbstring|bcmath|fileinfo|curl"
composer --version
```

### PHP قدیمی‌تر از 8.4.1؟
روی **کامپیوتر خودت** (نه هاست)، در `composer.json` این را اضافه کن (نسخه را با نسخهٔ هاست عوض کن):
```json
"config": { "platform": { "php": "8.3.0" } }
```
بعد `composer update` بزن، تست‌ها را اجرا کن (`php artisan test`) و commit کن. اگر Composer گفت نسخه‌ای سازگار پیدا نمی‌شود، هاست باید PHP را به‌روز کند. این کار در ROADMAP یک مورد جداگانه است.

---

## ۱. روی کامپیوتر خودت: آماده‌سازی

```bash
php artisan test        # همه باید سبز باشند
npm run build           # خروجی فرانت در public/build
```

پوشهٔ `public/build` در Git نیست (روی هاست Node نداریم)، پس **جداگانه آپلود می‌شود**. برای آپلود راحت‌تر، یک فایل zip از آن بساز.

---

## ۲. گرفتن کد روی هاست

با SSH وارد هاست شو. کد را **بیرون از پوشهٔ عمومی وب** بگذار:

```bash
cd ~
git clone git@github.com:Alisarlak71/ilnia.git ilnia-shop
cd ilnia-shop
```

- اگر مخزن خصوصی است، اول یک **deploy key** (کلید SSH فقط‌خواندنی) روی هاست بساز و در GitHub به مخزن اضافه کن: `Settings → Deploy keys`.
- اگر Git روی هاست نیست، کل پروژه را بدون `vendor`، `node_modules` و `.env` zip کن، آپلود کن و همان‌جا از zip باز کن.

بعد پوشهٔ `public/build` را از کامپیوترت آپلود کن، با مسیر نهایی `~/ilnia-shop/public/build`.

```bash
composer install --no-dev --optimize-autoloader
```

---

## ۳. ریشهٔ وب سایت (document root)

### روش «الف» (بهترین): ریشهٔ دامنه را روی `public/` بگذار
در پنل هاست (cPanel یا DirectAdmin)، document root دامنه را روی `/home/USER/ilnia-shop/public` تنظیم کن. **بعد برو سراغ بخش ۴.**

### روش «ب»: هاست اجازه نمی‌دهد و فقط `public_html` دارد
**اگر symlink مجاز است:**
```bash
mv ~/public_html ~/public_html.old            # فقط اگر خالی یا بی‌اهمیت است؛ اول محتوایش را چک کن
ln -s ~/ilnia-shop/public ~/public_html
```

**اگر symlink مجاز نیست:**
1. محتوای `~/ilnia-shop/public/` را (همراه `build/` و فایل `.htaccess`) در `~/public_html/` کپی کن.
2. فایل `~/public_html/index.php` را باز کن و مسیرها را به پروژه برسان. خط‌های `require` را این‌طور عوض کن:
   ```php
   if (file_exists($maintenance = __DIR__.'/../ilnia-shop/storage/framework/maintenance.php')) {
       require $maintenance;
   }

   require __DIR__.'/../ilnia-shop/vendor/autoload.php';

   /** @var Application $app */
   $app = require_once __DIR__.'/../ilnia-shop/bootstrap/app.php';
   $app->usePublicPath(__DIR__);   // public_html به‌جای ilnia-shop/public

   $app->handleRequest(Request::capture());
   ```
3. بعد از هر `npm run build`، پوشهٔ `build/` را در `~/public_html/build` آپلود کن (نه در `ilnia-shop/public`).
4. در بخش ۶، `PUBLIC_STORAGE_PATH=/home/USER/public_html/storage` را تنظیم کن.

**هرگز کل پروژه را داخل `public_html` نگذار.** در آن صورت فایل `.env` (رمزها) از اینترنت خوانده می‌شود.

---

## ۴. دیتابیس و `.env`

1. در پنل هاست یک دیتابیس MySQL و یک کاربر بساز و همهٔ دسترسی‌های آن دیتابیس را به کاربر بده.
2. فایل `.env` را بساز:
   ```bash
   cp .env.example .env
   nano .env
   ```
3. مقدارها را طبق `docs/SECURITY.md` (بخش ۱) پر کن. مهم‌ترین‌ها:
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://ilnia.ir
   LOG_LEVEL=error

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...

   SESSION_DRIVER=database
   SESSION_SECURE_COOKIE=true
   QUEUE_CONNECTION=database
   CACHE_STORE=database

   SMS_DRIVER=kavenegar
   KAVENEGAR_API_KEY=...
   KAVENEGAR_OTP_TEMPLATE=otp
   KAVENEGAR_ORDER_TEMPLATE=order

   ZARINPAL_MERCHANT_ID=          # تا گرفتن درگاه خالی بماند
   ZARINPAL_SANDBOX=false

   # TRUSTED_PROXIES=*            # فقط اگر سایت پشت CDN (آروان، کلادفلر) است
   ```
4. کلید برنامه و جدول‌ها:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   ```
   - `db:seed` در تولید فقط روش ارسال «پست» و پیش‌نویس صفحه‌ها را می‌سازد. **محصول نمونه فقط در محیط local ساخته می‌شود.**
   - `--force` لازم است، چون Laravel در تولید برای اجرای این دستورها تأیید می‌خواهد.
5. مدیر پنل:
   ```bash
   php artisan ilnia:make-admin
   ```

---

## ۵. دسترسی پوشه‌ها

```bash
chmod -R 775 storage bootstrap/cache
```

اگر صفحهٔ سفید یا خطای ۵۰۰ دیدی، معمولاً مشکل همین دسترسی‌هاست (بخش «عیب‌یابی» را ببین).

---

## ۶. عکس‌های آپلودشده (storage)

**اگر symlink مجاز است:**
```bash
php artisan storage:link
```

**اگر symlink مجاز نیست:** در `.env` مسیر کامل پوشهٔ `storage` داخل ریشهٔ وب را بده تا عکس‌ها مستقیم همان‌جا ذخیره شوند:
```dotenv
PUBLIC_STORAGE_PATH=/home/USER/ilnia-shop/public/storage     # روش الف
# PUBLIC_STORAGE_PATH=/home/USER/public_html/storage         # روش ب
```
بعد پوشه را بساز: `mkdir -p` همان مسیر.

---

## ۷. بهینه‌سازی

```bash
php artisan optimize          # کش تنظیمات، routeها و viewها
php artisan filament:optimize # کش کامپوننت‌ها و آیکن‌های پنل
```

**مهم:** بعد از هر تغییر در `.env`، دوباره `php artisan optimize` بزن. وگرنه تنظیم قبلی از کش خوانده می‌شود.

---

## ۸. کرون جاب (یک خط)

در پنل هاست، بخش Cron Jobs، این را با زمان‌بندی **هر دقیقه** (`* * * * *`) اضافه کن:

```bash
cd /home/USER/ilnia-shop && php artisan schedule:run >> /dev/null 2>&1
```

- اگر هاست چند نسخه PHP دارد، به‌جای `php` مسیر کامل نسخهٔ درست را بنویس. پشتیبانی هاست مسیر را می‌دهد، مثلاً `/usr/local/bin/php8.5`.
- همین یک خط این کارها را انجام می‌دهد (`routes/console.php`):
  - صف را هر دقیقه اجرا می‌کند (پیامک تأیید سفارش)؛
  - هر ساعت سفارش‌های پرداخت‌نشدهٔ قدیمی را لغو می‌کند؛
  - هر روز کدهای ورود قدیمی را پاک می‌کند.
- چک کن: `php artisan schedule:list`

---

## ۹. HTTPS
1. در پنل هاست، SSL رایگان (Let's Encrypt) را برای `ilnia.ir` و `www.ilnia.ir` فعال کن.
2. ریدایرکت `http → https` را روشن کن. بیشتر پنل‌ها گزینهٔ «Force HTTPS» دارند.
3. `APP_URL` باید با `https://` باشد (بخش ۴).

---

## ۱۰. چک نهایی

```bash
php artisan ilnia:security-check
```
**هیچ ✗ نباید بماند.** بعد این‌ها را در مرورگر چک کن:

- [ ] `https://ilnia.ir`: صفحهٔ اول با فونت و رنگ درست باز می‌شود.
- [ ] `https://ilnia.ir/.env`: باید خطای ۴۰۴ بدهد، **نه محتوای فایل**.
- [ ] `https://ilnia.ir/robots.txt`: خط `Sitemap:` را دارد.
- [ ] `https://ilnia.ir/admin`: ورود مدیر کار می‌کند. در پنل یک محصول با عکس بساز و ببین عکس در سایت دیده می‌شود.
- [ ] ورود با موبایل: کد پیامکی واقعاً می‌رسد. این بعد از تعریف الگوها در کاوه‌نگار ممکن است (فاز ۴).
- [ ] صفحه‌های «دربارهٔ ما»، «تماس»، «قوانین» و «شرایط ارسال» را در پنل کامل و منتشر کن.
- [ ] پشتیبان‌گیری خودکار دیتابیس را در پنل هاست روشن کن.

---

## به‌روزرسانی سایت (بعد از هر تغییر)

روی کامپیوتر خودت:
```bash
php artisan test
npm run build      # اگر ظاهر سایت یا فایل‌های CSS/JS عوض شده
git push
```

روی هاست:
```bash
cd ~/ilnia-shop
php artisan down                       # صفحهٔ «به‌زودی برمی‌گردیم» (۵۰۳)
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan filament:optimize
php artisan queue:restart
php artisan up
```

اگر `npm run build` زده‌ای، پوشهٔ `public/build` را هم دوباره آپلود کن. در روش «ب» مسیرش `public_html/build` است.

---

## عیب‌یابی

| نشانه | علت محتمل | راه‌حل |
|---|---|---|
| صفحهٔ سفید یا «مشکلی پیش آمد» (۵۰۰) | دسترسی پوشه‌ها، `.env` ناقص یا کش قدیمی | `tail -50 storage/logs/laravel.log` را ببین؛ `chmod -R 775 storage bootstrap/cache`؛ `php artisan optimize:clear` و بعد `php artisan optimize` |
| سایت بدون رنگ و فونت | `public/build` آپلود نشده یا جای اشتباه رفته | بخش ۱ و ۲ (روش «ب»: `public_html/build`) |
| عکس محصولات باز نمی‌شود | `storage:link` اجرا نشده یا `APP_URL` غلط است | بخش ۶؛ `APP_URL` باید دقیقاً نشانی سایت باشد |
| کد ورود نمی‌رسد | `SMS_DRIVER=log`، کلید یا الگوی کاوه‌نگار غلط | `ilnia:security-check`؛ در لاگ دنبال `Kavenegar` بگرد |
| «کمی صبر کنید» (۴۲۹) برای همه | سایت پشت CDN است و `TRUSTED_PROXIES` تنظیم نشده | `TRUSTED_PROXIES=*` و بعد `php artisan optimize` |
| پیامک تأیید سفارش دیر یا هرگز نمی‌رسد | کرون اجرا نمی‌شود | بخش ۸؛ `php artisan schedule:list`؛ جدول `jobs` نباید پر بماند |
| تغییر `.env` اثر ندارد | کش تنظیمات | `php artisan optimize` |
| پنل مدیریت بدون استایل | فایل‌های Filament منتشر نشده‌اند | `php artisan filament:assets` |
