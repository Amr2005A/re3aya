<?php
namespace App\Actions;
use App\Enums\PointTransactionType;
use App\Models\{PointTransaction, Student, User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class ReversePoints {
    /** لا حذف ولا تعديل لقيد النقاط: يُنشأ قيد معاكس يشير إلى الأصلي (ولا يُعكس القيد مرتين) */
    public function __invoke(User $actor, PointTransaction $tx): PointTransaction {
        return DB::transaction(function () use ($actor, $tx) {
            Student::whereKey($tx->student_id)->lockForUpdate()->first();
            $tx = PointTransaction::lockForUpdate()->findOrFail($tx->id);
            if ($tx->type !== PointTransactionType::Award) throw ValidationException::withMessages(['reverse' => 'لا يمكن عكس هذا النوع من القيود.']);
            if (PointTransaction::where('reverses_id', $tx->id)->exists()) throw ValidationException::withMessages(['reverse' => 'هذا القيد معكوس مسبقاً.']);
            $rev = PointTransaction::create(['student_id' => $tx->student_id, 'points' => -$tx->points, 'type' => PointTransactionType::Reversal,
                'reason' => 'عكس: ' . $tx->reason, 'circle_id' => $tx->circle_id, 'criteria_id' => $tx->criteria_id, 'reverses_id' => $tx->id,
                'awarded_by' => $actor->id, 'occurred_at' => now()]);
            Student::whereKey($tx->student_id)->increment('points_balance', -$tx->points);
            AuditLogger::log('points.reversed', $rev, ['original_id' => $tx->id, 'points' => $tx->points], ['points' => -$tx->points]);
            return $rev;
        });
    }
}
