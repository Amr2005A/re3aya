<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinanceCategory extends Model {
    
    protected $table = 'finance_categories';
    protected $fillable = ['name','type','is_active'];
    protected $casts = ['is_active'=>'boolean','type'=>\App\Enums\FinanceType::class];
    public function transactions() { return $this->hasMany(FinanceTransaction::class, 'category_id'); }
}
