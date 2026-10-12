<?php
namespace App\Actions;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class SaveUser {
    public function __invoke(array $data, ?User $user = null): User {
        return DB::transaction(function () use ($data, $user) {
            if (blank($data['password'] ?? null)) unset($data['password']);
            $old = $user?->only(['name', 'username', 'email', 'is_active']);
            $user ??= new User();
            $user->fill($data)->save();
            AuditLogger::log($old ? 'user.updated' : 'user.created', $user, $old, $user->only(['name', 'username', 'email', 'is_active']));
            return $user;
        });
    }
}
