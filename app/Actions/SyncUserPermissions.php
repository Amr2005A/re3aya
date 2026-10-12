<?php
namespace App\Actions;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class SyncUserPermissions {
    /** المانح (غير المالك) لا يستطيع منح صلاحية لا يملكها هو */
    public static function grantable(User $grantor): array {
        $all = collect(config('riaya-permissions'))->flatMap(fn ($g) => array_keys($g['items']));
        return $grantor->is_owner ? $all->all() : $all->intersect($grantor->getAllPermissions()->pluck('name'))->values()->all();
    }
    public function __invoke(User $actor, User $target, array $requested): void {
        DB::transaction(function () use ($actor, $target, $requested) {
            $grantable = self::grantable($actor);
            $old = $target->getPermissionNames()->all();
            // الصلاحيات التي لا يملك المانح التحكم بها تبقى كما هي
            $kept = array_diff($old, $grantable);
            $new = array_values(array_unique([...$kept, ...array_intersect($requested, $grantable)]));
            $target->syncPermissions($new);
            AuditLogger::log('user.permissions_changed', $target, ['permissions' => $old], ['permissions' => $new]);
        });
    }
}
