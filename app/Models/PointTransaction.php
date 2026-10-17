<?php
namespace App\Models;
use App\Enums\PointTransactionType;
use Illuminate\Database\Eloquent\Model;
class PointTransaction extends Model {
    protected $table = 'point_transactions';
    protected $fillable = ['student_id', 'points', 'type', 'reason', 'circle_id', 'criteria_id', 'badge_award_id', 'reverses_id', 'awarded_by', 'occurred_at', 'note'];
    protected $casts = ['occurred_at' => 'datetime', 'points' => 'integer', 'type' => PointTransactionType::class];
    public function student() { return $this->belongsTo(Student::class)->withTrashed(); }
    public function circle() { return $this->belongsTo(Circle::class)->withTrashed(); }
    public function criterion() { return $this->belongsTo(CircleCriterion::class, 'criteria_id')->withTrashed(); }
    public function awarder() { return $this->belongsTo(User::class, 'awarded_by')->withTrashed(); }
    public function reverses() { return $this->belongsTo(self::class, 'reverses_id'); }
    public function reversedBy() { return $this->hasOne(self::class, 'reverses_id'); }
    /** يُعكس القيد إن كان منحاً عادياً لم يُعكس بعد (قيود الأوسمة تُلغى من الوسام نفسه) */
    public function isReversible(): bool {
        if ($this->type !== PointTransactionType::Award) return false;
        return $this->relationLoaded('reversedBy') ? $this->reversedBy === null : ! $this->reversedBy()->exists();
    }
}
