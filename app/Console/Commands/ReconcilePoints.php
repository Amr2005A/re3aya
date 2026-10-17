<?php
namespace App\Console\Commands;
use App\Models\{PointTransaction, Student};
use App\Services\AuditLogger;
use Illuminate\Console\Command;
class ReconcilePoints extends Command {
    protected $signature = 'riaya:reconcile-points {--fix : صحّح الأرصدة المخزّنة لتطابق السجل}';
    protected $description = 'يقارن رصيد كل طالب المخزّن بمجموع قيود سجل النقاط';
    public function handle(): int {
        $sums = PointTransaction::selectRaw('student_id, SUM(points) AS s')->groupBy('student_id')->pluck('s', 'student_id');
        $bad = [];
        foreach (Student::withTrashed()->get(['id', 'name', 'points_balance']) as $st) {
            $real = (int) ($sums[$st->id] ?? 0);
            if ((int) $st->points_balance !== $real) $bad[] = [$st->id, $st->name, (int) $st->points_balance, $real];
        }
        if (! $bad) { $this->info('الأرصدة كلها مطابقة للسجل.'); return self::SUCCESS; }
        $this->table(['ID', 'الطالب', 'المخزّن', 'من السجل'], $bad);
        if (! $this->option('fix')) { $this->error(count($bad) . ' طالب غير مطابق. شغّل الأمر مع --fix للتصحيح.'); return self::FAILURE; }
        foreach ($bad as [$id, , $stored, $real]) {
            Student::withTrashed()->whereKey($id)->update(['points_balance' => $real]);
            AuditLogger::log('points.reconciled', Student::withTrashed()->find($id), ['points_balance' => $stored], ['points_balance' => $real]);
        }
        $this->info('تم تصحيح ' . count($bad) . ' رصيد.'); return self::SUCCESS;
    }
}
