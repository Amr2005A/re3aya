<?php
namespace App\Policies;
use App\Models\{PointTransaction, Student, User};
class PointTransactionPolicy {
    /** الطالب ضمن نطاق المستخدم: الكل لمن يرى كل الطلاب، وإلا طلاب مشرفه فقط */
    private function manages(User $u, ?Student $s): bool {
        if (! $s) return false;
        return $u->is_owner || $u->can('students.view-all') || ($u->supervisor && $s->supervisor_id === $u->supervisor->id);
    }
    public function viewAny(User $u): bool { return $u->is_owner || $u->can('points.view'); }
    public function award(User $u, Student $s): bool { return $u->can('points.award') && $this->manages($u, $s); }
    public function reverse(User $u, PointTransaction $t): bool { return $u->can('points.reverse') && $this->manages($u, $t->student); }
}
