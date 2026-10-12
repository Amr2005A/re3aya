<?php
namespace App\Actions;
use App\Models\{Supervisor, User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class SaveSupervisor {
    public function __invoke(User $actor, array $data, ?Supervisor $sup = null): Supervisor {
        return DB::transaction(function () use ($actor, $data, $sup) {
            $userData = array_intersect_key($data, array_flip(['name', 'email', 'username', 'password']));
            if (blank($userData['password'] ?? null)) unset($userData['password']);
            if ($sup) {
                $sup->user->fill($userData)->save(); $sup->update(['phone' => $data['phone'] ?? null]);
                AuditLogger::log('supervisor.updated', $sup);
                return $sup;
            }
            $user = new User($userData); $user->save();
            $sup = Supervisor::create(['user_id' => $user->id, 'phone' => $data['phone'] ?? null]);
            $user->givePermissionTo(array_intersect(config('riaya.supervisor_template'), SyncUserPermissions::grantable($actor)));
            AuditLogger::log('supervisor.created', $sup, null, ['user_id' => $user->id]);
            return $sup;
        });
    }
}
