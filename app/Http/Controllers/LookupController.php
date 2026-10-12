<?php
namespace App\Http\Controllers;
use App\Models\{GradeLevel, Section};
use App\Services\AuditLogger;
use Illuminate\Http\Request;
class LookupController extends Controller {
    private function model(string $type): string { return ['grades' => GradeLevel::class, 'sections' => Section::class][$type] ?? abort(404); }
    public function index() { return view('settings.lookups', ['grades' => GradeLevel::orderBy('sort_order')->get(), 'sections' => Section::orderBy('sort_order')->get()]); }
    public function store(Request $r, string $type) {
        $d = $r->validate(['name' => ['required', 'string', 'max:100'], 'sort_order' => ['nullable', 'integer', 'min:0']]);
        $m = $this->model($type)::create(['name' => $d['name'], 'sort_order' => $d['sort_order'] ?? 0]);
        AuditLogger::log("lookup.$type.created", $m, null, $m->only('name'));
        return back()->with('success', 'تمت الإضافة.');
    }
    public function update(Request $r, string $type, int $id) {
        $d = $r->validate(['name' => ['required', 'string', 'max:100'], 'sort_order' => ['nullable', 'integer', 'min:0']]);
        $m = $this->model($type)::findOrFail($id); $old = $m->only('name', 'sort_order');
        $m->update(['name' => $d['name'], 'sort_order' => $d['sort_order'] ?? 0]);
        AuditLogger::log("lookup.$type.updated", $m, $old, $m->only('name', 'sort_order'));
        return back()->with('success', 'تم الحفظ.');
    }
    public function toggle(string $type, int $id) {
        $m = $this->model($type)::findOrFail($id); $m->update(['is_active' => ! $m->is_active]);
        AuditLogger::log("lookup.$type.toggled", $m, null, ['is_active' => $m->is_active]);
        return back()->with('success', 'تم تغيير الحالة.');
    }
}
