<?php
namespace App\Http\Controllers;
use App\Http\Requests\SaveCircleRequest;
use App\Models\Circle;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\{DB, Gate};
class CircleController extends Controller {
    public function index() {
        Gate::authorize('viewAny', Circle::class);
        return view('circles.index', ['circles' => Circle::withCount('criteria')->orderBy('sort_order')->orderBy('id')->get()]);
    }
    public function create() { Gate::authorize('create', Circle::class); return view('circles.form', ['circle' => new Circle(['is_active' => true])]); }
    public function store(SaveCircleRequest $r) {
        Gate::authorize('create', Circle::class);
        $c = Circle::create($r->validated() + ['sort_order' => 0]);
        AuditLogger::log('circle.created', $c, null, $c->only('name'));
        return redirect()->route('circles.edit', $c)->with('success', 'تمت إضافة الحلقة، أضف بنود التقييم الآن.');
    }
    public function edit(Circle $circle) { Gate::authorize('viewAny', Circle::class); return view('circles.form', ['circle' => $circle->load('criteria')]); }
    public function update(SaveCircleRequest $r, Circle $circle) {
        Gate::authorize('update', Circle::class);
        $circle->fill($r->validated()); $old = array_intersect_key($circle->getOriginal(), $circle->getDirty()); $new = $circle->getDirty(); $circle->save();
        if ($new) AuditLogger::log('circle.updated', $circle, $old, $new);
        return redirect()->route('circles.index')->with('success', 'تم حفظ الحلقة.');
    }
    public function destroy(Circle $circle) {
        Gate::authorize('delete', Circle::class);
        DB::transaction(function () use ($circle) { $circle->criteria()->delete(); $circle->delete(); AuditLogger::log('circle.deleted', $circle, $circle->only('name')); });
        return redirect()->route('circles.index')->with('success', 'تم حذف الحلقة حذفاً ناعماً. القيود التاريخية لم تتأثر.');
    }
}
