<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Section extends Model {
    
    protected $table = 'sections';
    protected $fillable = ['grade_level_id','name','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function gradeLevel() { return $this->belongsTo(GradeLevel::class); }
}
