<?php
// المصدر الوحيد للصلاحيات وتسمياتها العربية — يستخدمه الـ Seeder وصفحة الصلاحيات
return [
    'dashboard' => ['label' => 'لوحة التحكم', 'items' => ['dashboard.view' => 'مشاهدة لوحة التحكم']],
    'users' => ['label' => 'المستخدمون', 'items' => [
        'users.view' => 'مشاهدة المستخدمين', 'users.create' => 'إضافة مستخدم', 'users.update' => 'تعديل مستخدم',
        'users.disable' => 'تعطيل/تفعيل مستخدم', 'users.manage-permissions' => 'إدارة الصلاحيات']],
    'students' => ['label' => 'الطلاب', 'items' => [
        'students.view-all' => 'مشاهدة كل الطلاب', 'students.view-own' => 'مشاهدة طلابه فقط', 'students.create' => 'إضافة طالب',
        'students.update' => 'تعديل طالب', 'students.delete' => 'حذف طالب', 'students.export' => 'تصدير الطلاب']],
    'supervisors' => ['label' => 'المشرفون', 'items' => [
        'supervisors.view' => 'مشاهدة المشرفين', 'supervisors.create' => 'إضافة مشرف', 'supervisors.update' => 'تعديل مشرف',
        'supervisors.disable' => 'تعطيل مشرف', 'supervisors.assign-students' => 'توزيع الطلاب']],
    'attendance' => ['label' => 'الحضور', 'items' => [
        'attendance.view-all' => 'مشاهدة كل الحضور', 'attendance.view-own' => 'مشاهدة حضور طلابه', 'attendance.record' => 'تسجيل الحضور',
        'attendance.update' => 'تعديل الحضور', 'attendance.report' => 'تقارير الحضور']],
    'notes' => ['label' => 'الملاحظات', 'items' => [
        'notes.view-all' => 'مشاهدة كل الملاحظات', 'notes.view-own' => 'مشاهدة ملاحظات طلابه', 'notes.create' => 'إضافة ملاحظة',
        'notes.update-own' => 'تعديل ملاحظاته', 'notes.delete' => 'حذف ملاحظة']],
    'circles' => ['label' => 'الحلقات والأنشطة', 'items' => [
        'circles.view' => 'مشاهدة الحلقات', 'circles.create' => 'إضافة حلقة', 'circles.update' => 'تعديل حلقة',
        'circles.delete' => 'حذف حلقة', 'circles.manage-criteria' => 'إدارة بنود التقييم']],
    'points' => ['label' => 'النقاط', 'items' => ['points.view' => 'مشاهدة النقاط', 'points.award' => 'منح نقاط', 'points.reverse' => 'عكس قيد نقاط']],
    'badges' => ['label' => 'الأوسمة', 'items' => [
        'badges.view' => 'مشاهدة الأوسمة', 'badges.create' => 'إنشاء وسام', 'badges.update' => 'تعديل وسام',
        'badges.disable' => 'تعطيل وسام', 'badges.delete' => 'حذف وسام', 'badges.award' => 'منح وسام']],
    'accounting' => ['label' => 'المحاسبة', 'items' => [
        'accounting.view' => 'مشاهدة المحاسبة', 'accounting.create' => 'تسجيل عملية', 'accounting.update' => 'تعديل عملية',
        'accounting.reverse' => 'عكس عملية', 'accounting.categories' => 'إدارة التصنيفات']],
    'reports' => ['label' => 'التقارير', 'items' => ['reports.view' => 'مشاهدة التقارير', 'reports.export' => 'تصدير التقارير']],
    'settings' => ['label' => 'الإعدادات', 'items' => ['settings.view' => 'مشاهدة الإعدادات', 'settings.update' => 'تعديل الإعدادات']],
    'audit' => ['label' => 'سجل العمليات', 'items' => ['audit.view' => 'مشاهدة سجل العمليات']],
];
