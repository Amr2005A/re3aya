<?php
namespace App\Services;
use App\Models\{User, WorkingDay};
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
class WorkingDayService {
    public function activeWeekdays(): array { return WorkingDay::where('is_active', true)->pluck('weekday')->map(fn ($d) => (int) $d)->all(); }
    public function isWorkingDay(CarbonInterface $date): bool { return in_array($date->dayOfWeek, $this->activeWeekdays(), true); }
    /** يغيّر أيام الدوام فقط، ولا يلمس سجلات الحضور السابقة إطلاقاً */
    public function sync(User $actor, array $days): void {
        DB::transaction(function () use ($days) {
            $old = $this->activeWeekdays();
            foreach (range(0, 6) as $d) WorkingDay::updateOrCreate(['weekday' => $d], ['is_active' => in_array($d, $days, true)]);
            AuditLogger::log('settings.working_days_changed', null, ['weekdays' => $old], ['weekdays' => array_values($days)]);
        });
    }
}
