<?php
namespace App\Policies;
use App\Models\{Note, Student, User};
class NotePolicy {
    private function sees(User $u, Student $s): bool {
        return $u->is_owner || $u->can('notes.view-all') || ($u->can('notes.view-own') && $u->supervisor && $s->supervisor_id === $u->supervisor->id);
    }
    public function viewAny(User $u): bool { return $u->is_owner || $u->can('notes.view-all') || $u->can('notes.view-own'); }
    public function view(User $u, Note $n): bool { return $this->sees($u, $n->student); }
    /** إضافة ملاحظة: صلاحية الإضافة + أن يكون الطالب ضمن ما يراه المستخدم */
    public function create(User $u, Student $s): bool { return $u->can('notes.create') && $this->sees($u, $s); }
    /** يعدّل ملاحظاته هو فقط (المالك مستثنى عبر Gate::before) */
    public function update(User $u, Note $n): bool { return $u->can('notes.update-own') && $n->author_id === $u->id && $this->sees($u, $n->student); }
    public function delete(User $u, Note $n): bool { return $u->can('notes.delete') && $this->sees($u, $n->student); }
}
