<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentSupervisorHistory extends Model {
    protected $table = 'student_supervisor_history';
    public $timestamps = false;
    protected $fillable = ['student_id', 'from_supervisor_id', 'to_supervisor_id', 'changed_by', 'reason', 'changed_at'];
    protected $casts = ['changed_at' => 'datetime'];
    public function student() { return $this->belongsTo(Student::class); }
    public function from() { return $this->belongsTo(Supervisor::class, 'from_supervisor_id'); }
    public function to() { return $this->belongsTo(Supervisor::class, 'to_supervisor_id'); }
    public function changer() { return $this->belongsTo(User::class, 'changed_by'); }
}
