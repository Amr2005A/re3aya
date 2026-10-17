<?php
namespace App\Http\Controllers;
use App\Actions\{AwardPoints, ReversePoints};
use App\Enums\PointTransactionType;
use App\Http\Requests\AwardPointRequest;
use App\Models\{Circle, CircleCriterion, PointTransaction, Student, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class PointController extends Controller {
    private function circles() { return Circle::where('is_active', true)->with('activeCriteria')->orderBy('sort_order')->get(); }
    public function index(Request $r) {
        Gate::authorize('viewAny', PointTransaction::class);
        $r->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);
        $base = PointTransaction::query()->whereIn('student_id', Student::managedBy($r->user())->select('id'))
            ->when($r->student, fn ($q, $v) => $q->where('student_id', $v))->when($r->circle, fn ($q, $v) => $q->where('circle_id', $v))
            ->when($r->type, fn ($q, $v) => $q->where('type', $v))->when($r->by, fn ($q, $v) => $q->where('awarded_by', $v))
            ->when($r->from, fn ($q, $v) => $q->whereDate('occurred_at', '>=', $v))->when($r->to, fn ($q, $v) => $q->whereDate('occurred_at', '<=', $v));
        $totals = (clone $base)->selectRaw('COALESCE(SUM(CASE WHEN points > 0 THEN points END),0) AS plus, COALESCE(SUM(CASE WHEN points < 0 THEN points END),0) AS minus, COALESCE(SUM(points),0) AS net, COUNT(*) AS c')->first();
        $rows = (clone $base)->with(['student', 'circle', 'criterion', 'awarder', 'reversedBy'])->orderByDesc('occurred_at')->orderByDesc('id')->paginate(20)->withQueryString();
        $awarders = User::whereIn('id', PointTransaction::query()->select('awarded_by'))->orderBy('name')->get(['id', 'name']);
        return view('points.index', ['rows' => $rows, 'totals' => $totals, 'circles' => Circle::withTrashed()->orderBy('name')->get(['id', 'name']),
            'awarders' => $awarders, 'types' => PointTransactionType::cases()]);
    }
    public function create(Request $r) {
        abort_unless($r->user()->is_owner || $r->user()->can('points.award'), 403);
        $students = Student::managedBy($r->user())->where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('points.create', ['students' => $students, 'selected' => $r->student, 'circles' => $this->circles()]);
    }
    public function store(AwardPointRequest $r, AwardPoints $award) {
        $student = Student::findOrFail($r->student_id);
        Gate::authorize('award', [PointTransaction::class, $student]);
        $criterion = $r->filled('criteria_id') ? CircleCriterion::find($r->criteria_id) : null;
        $circle = $r->filled('circle_id') ? Circle::find($r->circle_id) : null;
        $award($r->user(), $student, (int) $r->points, $r->filled('reason') ? $r->reason : $criterion->name, $circle, $criterion, $r->note);
        $back = $r->boolean('from_student') ? route('students.show', $student) . '#pts' : route('points.index');
        return redirect()->to($back)->with('success', 'تم تسجيل النقاط.');
    }
    public function reverse(PointTransaction $transaction, ReversePoints $reverse) {
        Gate::authorize('reverse', $transaction);
        $reverse(auth()->user(), $transaction);
        return back()->with('success', 'تم عكس القيد بقيد معاكس.');
    }
}
