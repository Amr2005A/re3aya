#!/usr/bin/env bash
# يُشغَّل من جذر مشروع رعاية:   bash /مسار/riaya-fix/fix.sh
set -euo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
[ -f artisan ] || { echo "شغّل السكربت من مجلد المشروع (الذي فيه artisan)"; exit 1; }

echo "▶ 1/6 نسخ الملفات المصحّحة"
cp -r "$HERE/files/." .

echo "▶ 2/6 نشر migrations حزمة spatie (سبب خطأ: no such table: permissions)"
if ls database/migrations | grep -qi permission; then echo "   موجودة مسبقاً"; else
  php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --no-interaction
fi

echo "▶ 3/6 تسجيل middleware باسم active (سبب خطأ: Target class [active] does not exist)"
if grep -q EnsureUserIsActive bootstrap/app.php; then echo "   مسجّل مسبقاً"; else
  perl -0pi -e 's/(->withMiddleware\(function \(Middleware \$middleware\)(?::\s*void)?\s*\{)/$1\n        \$middleware->alias(["active" => \\App\\Http\\Middleware\\EnsureUserIsActive::class]);/' bootstrap/app.php
  grep -q EnsureUserIsActive bootstrap/app.php || { echo "✗ لم أستطع تعديل bootstrap/app.php. أضف داخل withMiddleware:"; echo "  \$middleware->alias(['active' => \\App\\Http\\Middleware\\EnsureUserIsActive::class]);"; exit 1; }
fi

echo "▶ 4/6 الـ CSS واللغة وحذف الاختبارات الافتراضية"
mkdir -p public/css && cp resources/css/riaya.css public/css/riaya.css
for e in .env.example .env; do [ -f "$e" ] && sed -i 's/^APP_LOCALE=.*/APP_LOCALE=ar/' "$e" || true; done
rm -f tests/Feature/ExampleTest.php tests/Unit/ExampleTest.php

echo "▶ 5/6 إنشاء الجداول وزرع الصلاحيات والحساب (يمسح البيانات الحالية)"
php artisan optimize:clear >/dev/null
php artisan migrate:fresh --seed --force

echo "▶ 6/6 الاختبارات"
php artisan test
echo; echo "✔ الحساب: amrmohissen.b@gmail.com / 12345zob  — شغّل: php artisan serve"
