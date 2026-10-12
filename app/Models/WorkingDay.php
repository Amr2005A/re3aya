<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model {
    
    protected $table = 'working_days';
    protected $fillable = ['weekday','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public static function activeWeekdays(): array { return static::where('is_active', true)->pluck('weekday')->all(); }
}
