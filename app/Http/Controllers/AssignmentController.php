<?php
namespace App\Http\Controllers;
use App\Actions\TransferStudent;
use App\Models\{Student, Supervisor};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
class AssignmentController extends Controller {
    public function index(Request $r) {
        Gate::authorize('assign', Supervisor::class);
        $from = $r->query('from');
        $students = $from ? Student::with(['gradeLevel', 'section'])->where('supervisor_id', $from === 'none' ? null : $from)->orderBy('name')->get() : collect();
        return view('assignments.index', [
            'supervisors' => Supervisor::with('user')->withCount('students')->get()->sortBy('user.name'),
            'unassigned' => Student::whereNull('supervisor_id')->count(), 'from' => $from, 'students' => $students,
        ]);
    }
    public function transfer(Request $r, TransferStudent $transfer) {
        Gate::authorize('assign', Supervisor::class);
        $d = $r->validate([
            'student_ids' => ['required', 'array', 'min:1'], 'student_ids.*' => ['integer', 'exists:students,id'],
            'to_supervisor_id' => ['nullable', Rule::exists('supervisors', 'id')->where('is_active', true)], 'reason' => ['nullable', 'string', 'max:255'],
        ], ['student_ids.required' => 'حدّد طالباً واحداً على الأقل.']);
        $n = $transfer($r->user(), $d['student_ids'], $d['to_supervisor_id'] ?? null, $d['reason'] ?? null);
        return redirect()->route('assignments.index', ['from' => $r->from])->with('success', "تم نقل $n طالب.");
    }
}
