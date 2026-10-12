<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Badge extends Model {
    use SoftDeletes;
    protected $table = 'badges';
    protected $fillable = ['name','how_to_earn','points','description','icon_path','is_repeatable','is_active'];
    protected $casts = ['is_active'=>'boolean','is_repeatable'=>'boolean'];
    public function awards() { return $this->hasMany(BadgeAward::class); }
}
