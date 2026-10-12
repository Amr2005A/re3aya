<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Note extends Model {
    use SoftDeletes;
    protected $table = 'notes';
    protected $fillable = ['student_id','body'];
    protected $casts = [];
    public function student() { return $this->belongsTo(Student::class); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
