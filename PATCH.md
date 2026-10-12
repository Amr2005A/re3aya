# رقعة 1: إصلاح الـ Seeder + صفحة التسجيل
انسخ الملفات فوق مشروعك (مع الحفاظ على المسارات)، ثم:
```bash
php artisan config:clear
php artisan migrate:fresh --seed     # أو php artisan db:seed إن لم تكن تريد مسح البيانات
php artisan test
```
- سبب مشكلة الـ Seeder: `DatabaseSeeder` الافتراضي في Laravel لم يكن يستدعي `RiayaSeeder`، فلا تُزرع الصلاحيات ولا حساب المالك. أصبح يستدعيه الآن.
- التسجيل: `/register`. الحساب الجديد معطّل وبلا صلاحيات، ويظهر في «المستخدمون» (تنبيه بعدد الطلبات) لتفعّله وتحدد صلاحياته.
- لإغلاق التسجيل: ضع `RIAYA_REGISTRATION=false` في `.env`.
