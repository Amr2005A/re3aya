<?php
namespace App\Services;
class AttendanceStats {
    /**
     * نسبة الحضور = (حاضر + متأخر) ÷ (حاضر + متأخر + غائب).
     * الأيام "بعذر" لا تدخل في المقام. هذه هي القاعدة الوحيدة في النظام؛ غيّرها هنا فقط.
     */
    public static function rate(int $present, int $late, int $absent, int $excused = 0): ?float {
        $den = $present + $late + $absent;
        return $den > 0 ? round(($present + $late) / $den * 100, 1) : null;
    }
}
