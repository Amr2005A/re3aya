<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Circle extends Model {
    use SoftDeletes;
    protected $table = 'circles';
    protected $fillable = ['name', 'description', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function criteria() { return $this->hasMany(CircleCriterion::class)->orderBy('sort_order')->orderBy('id'); }
    public function activeCriteria() { return $this->criteria()->where('is_active', true); }
}
