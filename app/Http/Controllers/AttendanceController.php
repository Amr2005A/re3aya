<?php
namespace App\Http\Controllers;
use App\Actions\RecordBulkAttendance;
use App\Enums\AttendanceStatus;
use App\Http\Requests\SaveAttendanceRequest;
use App\Models\{AttendanceRecord, Student, Supervisor};
use App\Services\WorkingDayService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
class AttendanceController extends Controller {
    private function seesAll(): bool { $u = auth()->user(); return $u->is_owner || $u->can('attendance.view-all'); }
    private function supervisors() { return Supervisor::with('user')->get()->sortBy('user.name'); }

    public function index(Request $r, WorkingDayService $days) {
        Gate::authorize('viewAny', AttendanceRecord::class);
        $r->validate(['date' => ['nullable', 'date']]);
        $date = $r->filled('date') ? Carbon::parse($r->date)->startOfDay() : today();
        if ($date->gt(today())) $date = today();
        $u = $r->user();
        $hasRecords = AttendanceRecord::whereDate('date', $date)->exists();
        $students = Student::attendanceVisibleTo($u)->with(['gradeLevel', 'section'])
            ->where(fn ($q) => $q->where('status', 'active')->orWhereIn('id', AttendanceRecord::whereDate('date', $date)->select('student_id')))
            ->when($this->seesAll() && $r->supervisor, fn ($q, $v) => $q->where('supervisor_id', $v === 'none' ? null : $v))
            ->orderBy('name')->get();
        $records = AttendanceRecord::whereDate('date', $date)->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id');
        $isWorking = $days->isWorkingDay($date);
        return view('attendance.index', [
            'date' => $date, 'students' => $students, 'records' => $records, 'statuses' => AttendanceStatus::cases(),
            'isWorking' => $isWorking, 'closed' => ! $isWorking && ! $hasRecords,
            'canEdit' => Gate::allows('record', AttendanceRecord::class) && ($isWorking || $hasRecords),
            'canUpdate' => $u->is_owner || $u->can('attendance.update'),
            'seesAll' => $this->seesAll(), 'supervisors' => $this->supervisors(),
        ]);
    }
    public function store(SaveAttendanceRequest $r, RecordBulkAttendance $record) {
        Gate::authorize('record', AttendanceRecord::class);
        $res = $record($r->user(), Carbon::parse($r->date), $r->input('rows', []));
        $msg = "تم الحفظ: {$res['created']} جديد، {$res['updated']} معدَّل" . ($res['skipped'] ? "، {$res['skipped']} تم تجاهله لعدم الصلاحية" : '') . '.';
        return redirect()->route('attendance.index', ['date' => $r->date, 'supervisor' => $r->supervisor])->with('success', $msg);
    }
    private function filtered(Request $r) {
        $r->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);
        $from = $r->filled('from') ? Carbon::parse($r->from)->toDateString() : today()->subDays(30)->toDateString();
        $to = $r->filled('to') ? Carbon::parse($r->to)->toDateString() : today()->toDateString();
        $q = AttendanceRecord::query()->whereIn('student_id', Student::attendanceVisibleTo($r->user())->select('id'))
            ->whereBetween('date', [$from, $to])
            ->when($this->seesAll() && $r->supervisor, fn ($q, $v) => $q->where('supervisor_id', $v === 'none' ? null : $v));
        return [$q, $from, $to];
    }
    public function history(Request $r) {
        Gate::authorize('viewAny', AttendanceRecord::class);
        [$q, $from, $to] = $this->filtered($r);
        $records = $q->with(['student', 'supervisor.user', 'recorder'])
            ->when($r->status, fn ($q, $v) => $q->where('status', $v))
            ->when($r->q, fn ($q, $s) => $q->whereHas('student', fn ($w) => $w->where('name', 'like', "%$s%")))
            ->orderByDesc('date')->orderBy('student_id')->paginate(25)->withQueryString();
        return view('attendance.history', ['records' => $records, 'from' => $from, 'to' => $to, 'statuses' => AttendanceStatus::cases(), 'seesAll' => $this->seesAll(), 'supervisors' => $this->supervisors()]);
    }
    public function report(Request $r) {
        Gate::authorize('report', AttendanceRecord::class);
        [$q, $from, $to] = $this->filtered($r);
        $agg = "SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) AS absent_count,
                SUM(CASE WHEN status='late' THEN 1 ELSE 0 END) AS late_count, SUM(CASE WHEN status='excused' THEN 1 ELSE 0 END) AS excused_count";
        $totals = (clone $q)->selectRaw($agg)->first();
        $sort = ['absent' => 'absent_count', 'late' => 'late_count', 'present' => 'present_count'][$r->sort] ?? 'absent_count';
        $rows = (clone $q)->selectRaw("student_id, $agg")->groupBy('student_id')->orderByDesc($sort)->with('student')->paginate(20)->withQueryString();
        return view('attendance.report', ['rows' => $rows, 'totals' => $totals, 'from' => $from, 'to' => $to, 'seesAll' => $this->seesAll(), 'supervisors' => $this->supervisors()]);
    }
}
