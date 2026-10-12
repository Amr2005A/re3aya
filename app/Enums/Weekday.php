<?php
namespace App\Enums;
/** يطابق Carbon::dayOfWeek (0 = الأحد) */
enum Weekday: int {
    case Sunday = 0; case Monday = 1; case Tuesday = 2; case Wednesday = 3; case Thursday = 4; case Friday = 5; case Saturday = 6;
    public function label(): string {
        return match ($this) { self::Sunday => 'الأحد', self::Monday => 'الاثنين', self::Tuesday => 'الثلاثاء', self::Wednesday => 'الأربعاء',
            self::Thursday => 'الخميس', self::Friday => 'الجمعة', self::Saturday => 'السبت' };
    }
}
