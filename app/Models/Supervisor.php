<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Supervisor extends Model {
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'phone', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function students() { return $this->hasMany(Student::class); }
}
