<?php
return [
    // رمز الدولة بدون + أو 00 (مثال: 963). يُستخدم لتحويل الأرقام المحلية التي تبدأ بصفر إلى رابط WhatsApp.
    'country_code' => env('RIAYA_COUNTRY_CODE', ''),
    // صلاحيات تُمنح تلقائياً للمشرف الجديد (بحدود ما يملكه المانح)
    'supervisor_template' => ['dashboard.view', 'students.view-own', 'attendance.view-own', 'attendance.record',
        'notes.view-own', 'notes.create', 'notes.update-own', 'points.view', 'points.award'],
];
