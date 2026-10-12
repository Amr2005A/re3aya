<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model {
    
    protected $table = 'attendance_records';
    protected $fillable = ['student_id','date','status','supervisor_id','recorded_by','recorded_at','note'];
    protected $casts = ['date'=>'date','recorded_at'=>'datetime','status'=>\App\Enums\AttendanceStatus::class];
    public function student() { return $this->belongsTo(Student::class); }
    public function supervisor() { return $this->belongsTo(Supervisor::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}
