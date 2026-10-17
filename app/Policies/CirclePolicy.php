<?php
namespace App\Policies;
use App\Models\User;
class CirclePolicy {
    private function can(User $u, string $p): bool { return $u->is_owner || $u->can($p); }
    public function viewAny(User $u): bool { return $this->can($u, 'circles.view'); }
    public function create(User $u): bool { return $this->can($u, 'circles.create'); }
    public function update(User $u): bool { return $this->can($u, 'circles.update'); }
    public function delete(User $u): bool { return $this->can($u, 'circles.delete'); }
    public function manageCriteria(User $u): bool { return $this->can($u, 'circles.manage-criteria'); }
}
