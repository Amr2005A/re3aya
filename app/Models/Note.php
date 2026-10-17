<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Note extends Model {
    use SoftDeletes;
    protected $table = 'notes';
    protected $fillable = ['student_id', 'body'];
    // author_id غير قابل للتعبئة: يُحدَّد دائماً من المستخدم الحالي داخل الـ Action
    public function student() { return $this->belongsTo(Student::class)->withTrashed(); }
    public function author() { return $this->belongsTo(User::class, 'author_id')->withTrashed(); }
    public function wasEdited(): bool { return $this->updated_at && $this->created_at && $this->updated_at->gt($this->created_at); }
    public function scopeVisibleTo(Builder $q, User $u): Builder {
        if ($u->is_owner || $u->can('notes.view-all')) return $q;
        if ($u->can('notes.view-own') && $u->supervisor) return $q->whereHas('student', fn ($s) => $s->where('supervisor_id', $u->supervisor->id));
        return $q->whereRaw('1 = 0');
    }
}
