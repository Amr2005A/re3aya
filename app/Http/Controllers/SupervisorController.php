<?php
namespace App\Http\Controllers;
use App\Actions\SaveSupervisor;
use App\Http\Requests\SaveSupervisorRequest;
use App\Models\Supervisor;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Gate;
class SupervisorController extends Controller {
    public function index() {
        Gate::authorize('viewAny', Supervisor::class);
        return view('supervisors.index', ['supervisors' => Supervisor::with('user')->withCount('students')->get()->sortBy('user.name')]);
    }
    public function create() { Gate::authorize('create', Supervisor::class); return view('supervisors.form', ['supervisor' => new Supervisor()]); }
    public function store(SaveSupervisorRequest $r, SaveSupervisor $save) {
        Gate::authorize('create', Supervisor::class);
        $s = $save($r->user(), $r->validated());
        return redirect()->route('supervisors.show', $s)->with('success', 'تمت إضافة المشرف وصلاحياته الافتراضية.');
    }
    public function show(Supervisor $supervisor) {
        Gate::authorize('viewAny', Supervisor::class);
        return view('supervisors.show', ['supervisor' => $supervisor->load('user'), 'students' => $supervisor->students()->with(['gradeLevel', 'section'])->orderBy('name')->paginate(20)]);
    }
    public function edit(Supervisor $supervisor) { Gate::authorize('update', Supervisor::class); return view('supervisors.form', ['supervisor' => $supervisor->load('user')]); }
    public function update(SaveSupervisorRequest $r, Supervisor $supervisor, SaveSupervisor $save) {
        Gate::authorize('update', Supervisor::class);
        $save($r->user(), $r->validated(), $supervisor);
        return redirect()->route('supervisors.index')->with('success', 'تم حفظ التعديلات.');
    }
    public function toggle(Supervisor $supervisor) {
        Gate::authorize('disable', Supervisor::class);
        $active = ! $supervisor->is_active;
        $supervisor->update(['is_active' => $active]); $supervisor->user->update(['is_active' => $active]);
        AuditLogger::log($active ? 'supervisor.enabled' : 'supervisor.disabled', $supervisor);
        return back()->with('success', $active ? 'تم تفعيل المشرف.' : 'تم تعطيل المشرف. طلابه يبقون مسندين إليه حتى تنقلهم.');
    }
}
