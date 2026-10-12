<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model {
    
    protected $table = 'finance_transactions';
    protected $fillable = ['type','category_id','amount','occurred_on','description','notes','student_id','recorded_by','reverses_id'];
    protected $casts = ['occurred_on'=>'date','amount'=>'decimal:2','type'=>\App\Enums\FinanceType::class];
    public function category() { return $this->belongsTo(FinanceCategory::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function student() { return $this->belongsTo(Student::class); }
}
