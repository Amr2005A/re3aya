<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BadgeAward extends Model {
    
    protected $table = 'badge_awards';
    protected $fillable = ['student_id','badge_id','points_awarded','awarded_by','awarded_at','note','batch_id'];
    protected $casts = ['awarded_at'=>'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
    public function badge() { return $this->belongsTo(Badge::class)->withTrashed(); }
    public function awarder() { return $this->belongsTo(User::class, 'awarded_by'); }
}
