<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model {
    
    protected $table = 'point_transactions';
    protected $fillable = ['student_id','points','type','reason','circle_id','criteria_id','badge_award_id','reverses_id','awarded_by','occurred_at','note'];
    protected $casts = ['occurred_at'=>'datetime','type'=>\App\Enums\PointTransactionType::class];
    public function student() { return $this->belongsTo(Student::class); }
    public function circle() { return $this->belongsTo(Circle::class)->withTrashed(); }
    public function criterion() { return $this->belongsTo(CircleCriterion::class, 'criteria_id')->withTrashed(); }
    public function awarder() { return $this->belongsTo(User::class, 'awarded_by'); }
    public function reverses() { return $this->belongsTo(self::class, 'reverses_id'); }
}
