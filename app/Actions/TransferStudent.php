<?php
namespace App\Actions;
use App\Models\{Student, StudentSupervisorHistory, User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class TransferStudent {
    /** ينقل الطلاب إلى مشرف (أو إلى "بدون مشرف" عند null). يُرجع عدد من نُقلوا فعلاً. */
    public function __invoke(User $actor, array $studentIds, ?int $toSupervisorId, ?string $reason = null): int {
        return DB::transaction(function () use ($actor, $studentIds, $toSupervisorId, $reason) {
            $moved = 0;
            foreach (Student::whereIn('id', $studentIds)->lockForUpdate()->get() as $s) {
                if ($s->supervisor_id === $toSupervisorId) continue;
                StudentSupervisorHistory::create(['student_id' => $s->id, 'from_supervisor_id' => $s->supervisor_id,
                    'to_supervisor_id' => $toSupervisorId, 'changed_by' => $actor->id, 'reason' => $reason]);
                $from = $s->supervisor_id;
                $s->update(['supervisor_id' => $toSupervisorId]);
                AuditLogger::log('student.transferred', $s, ['supervisor_id' => $from], ['supervisor_id' => $toSupervisorId]);
                $moved++;
            }
            return $moved;
        });
    }
}
