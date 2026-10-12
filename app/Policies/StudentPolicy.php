<?php
namespace App\Policies;
use App\Models\{Student, User};
class StudentPolicy {
    private function seesAll(User $u): bool { return $u->is_owner || $u->can('students.view-all'); }
    private function owns(User $u, Student $s): bool { return $u->can('students.view-own') && $u->supervisor && $s->supervisor_id === $u->supervisor->id; }
    public function viewAny(User $u): bool { return $this->seesAll($u) || $u->can('students.view-own'); }
    public function view(User $u, Student $s): bool { return $this->seesAll($u) || $this->owns($u, $s); }
    public function create(User $u): bool { return $u->can('students.create'); }
    public function update(User $u, Student $s): bool { return $u->can('students.update') && $this->view($u, $s); }
    public function delete(User $u, Student $s): bool { return $u->can('students.delete') && $this->view($u, $s); }
    public function viewAttendance(User $u, Student $s): bool {
        return $u->is_owner || $u->can('attendance.view-all') || ($u->can('attendance.view-own') && $u->supervisor && $s->supervisor_id === $u->supervisor->id);
    }
}
