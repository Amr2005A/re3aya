<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class DashboardController extends Controller {
    /** من لا يملك dashboard.view يُحوَّل لأول قسم يملك صلاحيته، وإلا تظهر له صفحة انتظار بدل 403 */
    public function __invoke(Request $r) {
        $u = $r->user();
        if ($u->is_owner || $u->can('dashboard.view')) return view('dashboard'); // المحتوى الفعلي في المرحلة 11
        $targets = [
            'students.index' => ['students.view-all', 'students.view-own'],
            'attendance.index' => ['attendance.record', 'attendance.update', 'attendance.view-all', 'attendance.view-own'],
            'supervisors.index' => ['supervisors.view'], 'users.index' => ['users.view'],
        ];
        foreach ($targets as $route => $perms) if (collect($perms)->contains(fn ($p) => $u->can($p))) return redirect()->route($route);
        return view('dashboard-empty');
    }
}
