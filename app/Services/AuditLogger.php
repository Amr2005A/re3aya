<?php
namespace App\Services;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
class AuditLogger {
    public static function log(string $action, ?Model $subject = null, ?array $old = null, ?array $new = null): void {
        AuditLog::create([
            'user_id' => auth()->id(), 'action' => $action,
            'auditable_type' => $subject?->getMorphClass(), 'auditable_id' => $subject?->getKey(),
            'old_values' => $old, 'new_values' => $new, 'ip' => request()->ip(),
        ]);
    }
}
