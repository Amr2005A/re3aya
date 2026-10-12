<?php
namespace App\Policies;
use App\Models\User;
class SupervisorPolicy {
    private function can(User $u, string $p): bool { return $u->is_owner || $u->can($p); }
    public function viewAny(User $u): bool { return $this->can($u, 'supervisors.view'); }
    public function create(User $u): bool { return $this->can($u, 'supervisors.create'); }
    public function update(User $u): bool { return $this->can($u, 'supervisors.update'); }
    public function disable(User $u): bool { return $this->can($u, 'supervisors.disable'); }
    public function assign(User $u): bool { return $this->can($u, 'supervisors.assign-students'); }
}
