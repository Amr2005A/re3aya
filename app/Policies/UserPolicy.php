<?php
namespace App\Policies;
use App\Models\User;
class UserPolicy {
    private function can(User $a, string $p): bool { return $a->is_owner || $a->can($p); }
    public function viewAny(User $a): bool { return $this->can($a, 'users.view'); }
    public function create(User $a): bool { return $this->can($a, 'users.create'); }
    public function update(User $a, User $t): bool { return $this->can($a, 'users.update') && (! $t->is_owner || $a->is_owner); }
    public function disable(User $a, User $t): bool { return $this->can($a, 'users.disable') && ! $t->is_owner && $a->isNot($t); }
    public function managePermissions(User $a, User $t): bool { return $this->can($a, 'users.manage-permissions') && ! $t->is_owner && $a->isNot($t); }
}
