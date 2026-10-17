<?php
namespace App\Models;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Student extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = ['name','father_name','address','phone','phone_extra','father_job',
        'grade_level_id','section_id','supervisor_id','status','registered_at'];
    // points_balance غير قابل للتعبئة: يُحدَّث فقط عبر Action منح النقاط
    protected $casts = ['status' => StudentStatus::class, 'registered_at' => 'date'];
    public function gradeLevel() { return $this->belongsTo(GradeLevel::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function supervisor() { return $this->belongsTo(Supervisor::class); }
    public function attendance() { return $this->hasMany(AttendanceRecord::class); }
    public function notes() { return $this->hasMany(Note::class); }
    public function pointTransactions() { return $this->hasMany(PointTransaction::class); }
    public function badgeAwards() { return $this->hasMany(BadgeAward::class); }
    public function financeTransactions() { return $this->hasMany(FinanceTransaction::class); }
    public function supervisorHistory() { return $this->hasMany(StudentSupervisorHistory::class); }
    public function scopeVisibleTo(Builder $q, User $u): Builder {
        if ($u->is_owner || $u->can('students.view-all')) return $q;
        if ($u->can('students.view-own') && $u->supervisor) return $q->where('supervisor_id', $u->supervisor->id);
        return $q->whereRaw('1 = 0');
    }
    public function scopeAttendanceVisibleTo(Builder $q, User $u): Builder {
        if ($u->is_owner || $u->can('attendance.view-all')) return $q;
        if ($u->supervisor && ($u->can('attendance.view-own') || $u->can('attendance.record') || $u->can('attendance.update'))) return $q->where('supervisor_id', $u->supervisor->id);
        return $q->whereRaw('1 = 0');
    }
    /** الطلاب الذين يستطيع المستخدم إدارة نقاطهم: الكل لمن يرى كل الطلاب، وإلا طلاب مشرفه */
    public function scopeManagedBy(Builder $q, User $u): Builder {
        if ($u->is_owner || $u->can('students.view-all')) return $q;
        return $u->supervisor ? $q->where('supervisor_id', $u->supervisor->id) : $q->whereRaw('1 = 0');
    }
    public function scopeSearch(Builder $q, ?string $s): Builder {
        return $s ? $q->where(fn ($w) => $w->where('name','like',"%$s%")->orWhere('father_name','like',"%$s%")->orWhere('phone','like',"%$s%")) : $q;
    }
}
