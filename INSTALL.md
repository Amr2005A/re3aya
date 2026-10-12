# رعاية — المراحل 2 إلى 5 مجمّعة
هذا المجلد يحتوي على ملفات المشروع فقط، وليس مشروع Laravel كاملاً. الملفات الأحدث تغلب الأقدم.

1. أنشئ مشروع Laravel جديداً، وثبّت: `composer require spatie/laravel-permission` ثم انشر migrations الحزمة.
2. انسخ محتوى هذا المجلد فوق المشروع (وافق على الاستبدال).
3. انسخ `resources/css/riaya.css` إلى `public/css/riaya.css`. ملف `public/css/attendance.css` موجود جاهزاً.
4. في `bootstrap/app.php` داخل `withMiddleware`:
   `$middleware->alias(['active' => \App\Http\Middleware\EnsureUserIsActive::class]);`
5. اضبط في `config/app.php`: `'locale' => 'ar'`، وفي `.env`: `RIAYA_COUNTRY_CODE=` (رمز الدولة بدون +).
6. `php artisan migrate:fresh --seed` ثم `php artisan test`.
7. ادخل بـ owner@riaya.test / change-me-now وغيّر كلمة المرور فوراً.
