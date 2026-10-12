<?php
namespace App\Policies;
use App\Models\User;
class AttendanceRecordPolicy {
    private function any(User $u, array $p): bool { return $u->is_owner || collect($p)->contains(fn ($x) => $u->can($x)); }
    public function viewAny(User $u): bool { return $this->any($u, ['attendance.view-all', 'attendance.view-own', 'attendance.record', 'attendance.update']); }
    public function record(User $u): bool { return $this->any($u, ['attendance.record', 'attendance.update']); }
    public function report(User $u): bool { return $this->any($u, ['attendance.report']); }
}
