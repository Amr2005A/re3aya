<?php
namespace App\Actions;
use App\Models\{Student, StudentSupervisorHistory, User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class SaveStudent {
    public function __invoke(User $actor, array $data, ?Student $student = null): Student {
        return DB::transaction(function () use ($actor, $data, $student) {
            $seesAll = $actor->is_owner || $actor->can('students.view-all');
            $canAssign = $actor->is_owner || $actor->can('supervisors.assign-students');
            $sup = $data['supervisor_id'] ?? null; unset($data['supervisor_id']);
            $data['registered_at'] ??= now()->toDateString();

            if (! $student) {
                // من لا يرى إلا طلابه يُسند الطالب الجديد إليه تلقائياً
                $sup = ($seesAll || $canAssign) ? $sup : $actor->supervisor?->id;
                $student = new Student($data); $student->supervisor_id = $sup; $student->save();
                if ($sup) StudentSupervisorHistory::create(['student_id' => $student->id, 'to_supervisor_id' => $sup, 'changed_by' => $actor->id, 'reason' => 'تسجيل أولي']);
                AuditLogger::log('student.created', $student, null, $student->only(['name', 'supervisor_id', 'status']));
                return $student;
            }
            $student->fill($data);
            $dirty = $student->getDirty(); $old = array_intersect_key($student->getOriginal(), $dirty);
            $student->save();
            if ($dirty) AuditLogger::log('student.updated', $student, $old, $dirty);
            if ($canAssign && $sup !== $student->supervisor_id) app(TransferStudent::class)($actor, [$student->id], $sup, 'تعديل بيانات الطالب');
            return $student->refresh();
        });
    }
}
