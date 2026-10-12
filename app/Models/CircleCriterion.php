<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CircleCriterion extends Model {
    use SoftDeletes;
    protected $table = 'circle_criteria';
    protected $fillable = ['circle_id','name','default_points','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function circle() { return $this->belongsTo(Circle::class); }
}
