# پنل مدیریت محصولات

پنل مدیریت RTL برای مدیران، دسته‌بندی‌های درختی و محصولات با Laravel و Bootstrap 5.

## پیش‌نیازها

- PHP 8.3 یا بالاتر
- MySQL 8 / MariaDB
- Composer
- افزونه‌های معمول Laravel شامل PDO MySQL، Mbstring، OpenSSL، Fileinfo، GD و Exif

## نصب

```bash
composer install
copy .env.example .env
php artisan key:generate
```

اطلاعات اتصال MySQL و مقادیر `ADMIN_NAME`، `ADMIN_MOBILE` و `ADMIN_PASSWORD` را در `.env` تنظیم کنید، سپس:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

برای دریافت نرخ ایران گلد، `IRANGOLD_API_TOKEN` را در `.env` تنظیم و Scheduler را اجرا کنید:

```bash
php artisan schedule:work
```

Command زیر توسط Scheduler هر دقیقه اجرا می‌شود و `data.moltens[0].sell_price` را در جدول `gold_prices` ذخیره می‌کند:

```bash
php artisan gold:sync-price
```

مقدار API به‌صورت خام ذخیره می‌شود و ابتدا بر ۱۰ تقسیم می‌شود تا نرخ تومان به دست آید. قیمت محصول با فرمول زیر محاسبه می‌شود:

```text
قیمت هر گرم = نرخ مثقال تومان ÷ 4.3318
قیمت نهایی = قیمت هر گرم × (1 + اجرت درصدی ÷ 100) × وزن
```

نرخ هر گرم در رابط کاربری برای خوانایی به نزدیک‌ترین هزار تومان رو به بالا گرد می‌شود؛ محاسبه قیمت محصول با نرخ دقیق انجام می‌گیرد.

در production باید Cron استاندارد Laravel هر دقیقه `php artisan schedule:run` را اجرا کند. گزینه `IRANGOLD_VERIFY_SSL=false` فقط برای PHP محلی با CA خراب است و در production باید `true` باشد.

پنل در `/login` در دسترس است. اطلاعات پیش‌فرض Seeder فقط برای توسعه:

- موبایل: `09120000000`
- رمز: `Admin@123456`

فهرست فروشگاه مستقیماً در `/` و جزئیات هر محصول با آدرس کوتاه `/p/{id}` در دسترس است. مسیر قدیمی `/shop` به آدرس اصلی هدایت می‌شود و فهرست با رسیدن کاربر به انتهای صفحه، صفحه بعد را خودکار دریافت می‌کند.

این مقادیر را پیش از اجرای Seeder در محیط واقعی تغییر دهید. فایل‌های CSS و JavaScript پنل از `public` سرو می‌شوند و اجرای npm الزامی نیست.

## آزمون و کیفیت کد

```bash
php artisan test
php vendor/bin/pint --test
```

اگر PDO SQLite در PHP غیرفعال است، آن را برای تست‌ها فعال کنید. نمونه اجرای موقت در ویندوز:

```bash
php -d extension=pdo_sqlite vendor/bin/pest
```

## نکات معماری

- منطق مدیر، دسته و محصول در `app/Services` قرار دارد.
- اعتبارسنجی و مجوزها به‌ترتیب در `app/Http/Requests` و `app/Policies` هستند.
- شمارنده `next_product_sequence` در تراکنش و با قفل سطری افزایش می‌یابد؛ بنابراین کد محصول در رقابت هم‌زمان تکراری نمی‌شود و پس از حذف دوباره مصرف نخواهد شد.
- تصویر اصلی و گالری روی دیسک `public` ذخیره می‌شوند و گالری حداکثر سه تصویر دارد.
- تصاویر محصولات هنگام آپلود با حفظ نسبت کوچک‌سازی، به WebP تبدیل و metadata آن‌ها حذف می‌شود. سقف ابعاد تصویر اصلی `1600×1600` و گالری `1200×1200` است.
- حذف دسته دارای محصول یا زیردسته در سرویس و Foreign Key مسدود است.
