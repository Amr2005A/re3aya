<?php
namespace App\Http\Controllers;
use App\Services\{AuditLogger, SettingsService};
use Illuminate\Http\Request;
class PointSettingsController extends Controller {
    public function edit() { return view('settings.points', ['allowNegative' => SettingsService::allowNegativePoints(), 'max' => SettingsService::maxPointsPerAward()]); }
    public function update(Request $r) {
        $d = $r->validate(['max_per_award' => ['required', 'integer', 'min:1', 'max:100000']]);
        $old = ['allow_negative' => SettingsService::allowNegativePoints(), 'max_per_award' => SettingsService::maxPointsPerAward()];
        SettingsService::put('points.allow_negative', $r->boolean('allow_negative'), 'points');
        SettingsService::put('points.max_per_award', (int) $d['max_per_award'], 'points');
        AuditLogger::log('settings.points_changed', null, $old, ['allow_negative' => $r->boolean('allow_negative'), 'max_per_award' => (int) $d['max_per_award']]);
        return back()->with('success', 'تم حفظ إعدادات النقاط.');
    }
}
