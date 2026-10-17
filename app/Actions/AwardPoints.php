<?php
namespace App\Actions;
use App\Enums\PointTransactionType;
use App\Models\{Circle, CircleCriterion, PointTransaction, Student, User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
class AwardPoints {
    /** يسجّل قيداً في الـ Ledger ويحدّث الرصيد المخزّن داخل Transaction واحدة */
    public function __invoke(User $actor, Student $student, int $points, string $reason, ?Circle $circle = null, ?CircleCriterion $criterion = null,
                             ?string $note = null, PointTransactionType $type = PointTransactionType::Award, ?int $badgeAwardId = null): PointTransaction {
        if ($points === 0) throw new InvalidArgumentException('عدد النقاط لا يمكن أن يكون صفراً.');
        return DB::transaction(function () use ($actor, $student, $points, $reason, $circle, $criterion, $note, $type, $badgeAwardId) {
            Student::whereKey($student->id)->lockForUpdate()->first();
            $tx = PointTransaction::create(['student_id' => $student->id, 'points' => $points, 'type' => $type, 'reason' => $reason,
                'circle_id' => $circle?->id ?? $criterion?->circle_id, 'criteria_id' => $criterion?->id, 'badge_award_id' => $badgeAwardId,
                'awarded_by' => $actor->id, 'occurred_at' => now(), 'note' => $note]);
            Student::whereKey($student->id)->increment('points_balance', $points);
            AuditLogger::log('points.awarded', $tx, null, ['student_id' => $student->id, 'points' => $points, 'reason' => $reason]);
            return $tx;
        });
    }
}
