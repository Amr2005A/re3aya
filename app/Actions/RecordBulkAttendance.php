<?php
namespace App\Actions;
use App\Enums\AttendanceStatus;
use App\Models\{AttendanceRecord, Student, User};
use App\Services\{AuditLogger, WorkingDayService};
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class RecordBulkAttendance {
    public function __construct(private WorkingDayService $days) {}
    /** @param array<int,array{status?:string,note?:string}> $rows مفتاحها student_id. يُرجع ['created','updated','skipped'] */
    public function __invoke(User $actor, CarbonInterface $date, array $rows): array {
        $day = $date->toDateString();
        if ($date->copy()->startOfDay()->gt(today())) throw ValidationException::withMessages(['date' => 'لا يمكن تسجيل حضور لتاريخ مستقبلي.']);
        // يوم ليس دواماً حالياً يُقبل فقط إن كانت له سجلات سابقة (دوام قديم تغيّرت أيامه)
        if (! $this->days->isWorkingDay($date) && ! AttendanceRecord::whereDate('date', $day)->exists())
            throw ValidationException::withMessages(['date' => 'هذا اليوم ليس من أيام الدوام.']);

        return DB::transaction(function () use ($actor, $day, $rows) {
            $canUpdate = $actor->is_owner || $actor->can('attendance.update');
            $students = Student::attendanceVisibleTo($actor)->whereIn('id', array_keys($rows))->get()->keyBy('id');
            $existing = AttendanceRecord::whereDate('date', $day)->whereIn('student_id', $students->keys())->get()->keyBy('student_id');
            $res = ['created' => 0, 'updated' => 0, 'skipped' => 0];
            foreach ($rows as $sid => $row) {
                $status = AttendanceStatus::tryFrom((string) ($row['status'] ?? ''));
                if (! $status) continue; // لم يُحدَّد: لا يُسجَّل شيء
                $student = $students->get((int) $sid);
                if (! $student) { $res['skipped']++; continue; }
                $note = filled($row['note'] ?? null) ? $row['note'] : null;
                $rec = $existing->get($student->id);
                if (! $rec) {
                    AttendanceRecord::create(['student_id' => $student->id, 'date' => $day, 'status' => $status, 'note' => $note,
                        'supervisor_id' => $student->supervisor_id, // لقطة وقت التسجيل
                        'recorded_by' => $actor->id, 'recorded_at' => now()]);
                    $res['created']++; continue;
                }
                if ($rec->status === $status && $rec->note === $note) continue;
                if (! $canUpdate) { $res['skipped']++; continue; }
                $old = ['status' => $rec->status->value, 'note' => $rec->note];
                $rec->update(['status' => $status, 'note' => $note]);
                AuditLogger::log('attendance.updated', $rec, $old, ['status' => $status->value, 'note' => $note, 'date' => $day]);
                $res['updated']++;
            }
            if ($res['created']) AuditLogger::log('attendance.recorded', null, null, ['date' => $day] + $res);
            return $res;
        });
    }
}
