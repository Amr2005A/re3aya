<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model {
    
    protected $table = 'grade_levels';
    protected $fillable = ['name','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function students() { return $this->hasMany(Student::class); }
}
