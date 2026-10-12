<?php
namespace App\Http\Controllers;
use App\Actions\SaveStudent;
use App\Enums\StudentStatus;
use App\Http\Requests\SaveStudentRequest;
use App\Models\{AuditLog, GradeLevel, Section, Student, Supervisor};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class StudentController extends Controller {
    private const SORTS = ['name', 'registered_at', 'points_balance'];
    public function index(Request $r) {
        Gate::authorize('viewAny', Student::class);
        $sort = in_array($r->sort, self::SORTS) ? $r->sort : 'name'; $dir = $r->dir === 'desc' ? 'desc' : 'asc';
        $students = Student::visibleTo($r->user())->with(['gradeLevel', 'section', 'supervisor.user'])->search($r->q)
            ->when($r->grade, fn ($q, $v) => $q->where('grade_level_id', $v))
            ->when($r->section, fn ($q, $v) => $q->where('section_id', $v))
            ->when($r->supervisor, fn ($q, $v) => $q->where('supervisor_id', $v === 'none' ? null : $v))
            ->when($r->status, fn ($q, $v) => $q->where('status', $v))
            ->orderBy($sort, $dir)->paginate(15)->withQueryString();
        return view('students.index', ['students' => $students] + $this->options());
    }
    public function create() { Gate::authorize('create', Student::class); return view('students.form', ['student' => new Student(['status' => 'active'])] + $this->options()); }
    public function store(SaveStudentRequest $r, SaveStudent $save) {
        Gate::authorize('create', Student::class);
        $s = $save($r->user(), $r->validated());
        return redirect()->route('students.show', $s)->with('success', 'تمت إضافة الطالب.');
    }
    public function show(Student $student) {
        Gate::authorize('view', $student);
        $student->load(['gradeLevel', 'section', 'supervisor.user', 'supervisorHistory.changer']);
        $timeline = $student->supervisorHistory->map(fn ($h) => ['at' => $h->changed_at, 'by' => $h->changer?->name,
            'text' => 'نقل المشرف: ' . ($h->from?->user->name ?? 'بدون') . ' ← ' . ($h->to?->user->name ?? 'بدون') . ($h->reason ? " ({$h->reason})" : '')]);
        $labels = ['student.created' => 'تسجيل الطالب', 'student.updated' => 'تعديل بيانات الطالب'];
        $audit = AuditLog::with('user')->where('auditable_type', $student->getMorphClass())->where('auditable_id', $student->id)
            ->whereIn('action', array_keys($labels))->get()->map(fn ($a) => ['at' => $a->created_at, 'by' => $a->user?->name, 'text' => $labels[$a->action]]);
        return view('students.show', ['student' => $student, 'timeline' => $timeline->concat($audit)->sortByDesc('at')->values()]);
    }
    public function edit(Student $student) { Gate::authorize('update', $student); return view('students.form', compact('student') + $this->options()); }
    public function update(SaveStudentRequest $r, Student $student, SaveStudent $save) {
        Gate::authorize('update', $student);
        $save($r->user(), $r->validated(), $student);
        return redirect()->route('students.show', $student)->with('success', 'تم حفظ التعديلات.');
    }
    public function destroy(Student $student) {
        Gate::authorize('delete', $student);
        $student->delete(); \App\Services\AuditLogger::log('student.deleted', $student);
        return redirect()->route('students.index')->with('success', 'تم حذف الطالب (حذف ناعم، تبقى سجلاته محفوظة).');
    }
    private function options(): array {
        return ['grades' => GradeLevel::where('is_active', true)->orderBy('sort_order')->get(), 'sections' => Section::where('is_active', true)->orderBy('sort_order')->get(),
            'supervisors' => Supervisor::with('user')->where('is_active', true)->get()->sortBy('user.name'), 'statuses' => StudentStatus::cases()];
    }
}
