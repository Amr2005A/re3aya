<?php
namespace App\Http\Controllers;
use App\Enums\Weekday;
use App\Services\WorkingDayService;
use Illuminate\Http\Request;
class WorkingDayController extends Controller {
    public function edit(WorkingDayService $s) { return view('settings.working-days', ['weekdays' => Weekday::cases(), 'active' => $s->activeWeekdays()]); }
    public function update(Request $r, WorkingDayService $s) {
        $d = $r->validate(['days' => ['required', 'array', 'min:1'], 'days.*' => ['integer', 'between:0,6']], ['days.required' => 'اختر يوم دوام واحداً على الأقل.']);
        $s->sync($r->user(), array_map('intval', $d['days']));
        return back()->with('success', 'تم حفظ أيام الدوام. سجلات الحضور السابقة لم تتأثر.');
    }
}
