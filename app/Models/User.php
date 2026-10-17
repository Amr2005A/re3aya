<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable {
    use Notifiable, SoftDeletes, HasRoles;
    protected $fillable = ['name','username','email','password','is_active'];
    protected $hidden = ['password','remember_token'];
    // is_owner غير قابل للتعبئة الجماعية عمداً
    protected $casts = ['password' => 'hashed', 'is_active' => 'boolean', 'is_owner' => 'boolean', 'last_login_at' => 'datetime'];
    public function supervisor() { return $this->hasOne(Supervisor::class); }
}
