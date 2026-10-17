<?php
namespace App\Http\Controllers;
use App\Http\Requests\SaveCriterionRequest;
use App\Models\{Circle, CircleCriterion};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Gate;
class CriterionController extends Controller {
    public function store(SaveCriterionRequest $r, Circle $circle) {
        Gate::authorize('manageCriteria', Circle::class);
        $c = $circle->criteria()->create($r->validated() + ['sort_order' => CircleCriterion::where('circle_id', $circle->id)->max('sort_order') + 1]);
        AuditLogger::log('criterion.created', $c, null, $c->only('name', 'default_points'));
        return back()->with('success', 'تمت إضافة البند.');
    }
    public function update(SaveCriterionRequest $r, CircleCriterion $criterion) {
        Gate::authorize('manageCriteria', Circle::class);
        $criterion->fill($r->validated()); $old = array_intersect_key($criterion->getOriginal(), $criterion->getDirty()); $new = $criterion->getDirty(); $criterion->save();
        if ($new) AuditLogger::log('criterion.updated', $criterion, $old, $new);
        return back()->with('success', 'تم حفظ البند.');
    }
    public function toggle(CircleCriterion $criterion) {
        Gate::authorize('manageCriteria', Circle::class);
        $criterion->update(['is_active' => ! $criterion->is_active]);
        AuditLogger::log('criterion.toggled', $criterion, null, ['is_active' => $criterion->is_active]);
        return back()->with('success', $criterion->is_active ? 'تم تفعيل البند.' : 'تم تعطيل البند (تبقى قيوده القديمة).');
    }
    public function destroy(CircleCriterion $criterion) {
        Gate::authorize('manageCriteria', Circle::class);
        $criterion->delete(); AuditLogger::log('criterion.deleted', $criterion, $criterion->only('name'));
        return back()->with('success', 'تم حذف البند حذفاً ناعماً. القيود القديمة محفوظة.');
    }
}
