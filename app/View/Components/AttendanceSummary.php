<?php
namespace App\View\Components;
use App\Models\{AttendanceRecord, Student};
use App\Services\AttendanceStats;
use Illuminate\View\Component;
class AttendanceSummary extends Component {
    public function __construct(public Student $student) {}
    public function render() {
        $c = AttendanceRecord::where('student_id', $this->student->id)->selectRaw('status, COUNT(*) AS c')->groupBy('status')->pluck('c', 'status');
        [$p, $l, $a, $e] = [(int) ($c['present'] ?? 0), (int) ($c['late'] ?? 0), (int) ($c['absent'] ?? 0), (int) ($c['excused'] ?? 0)];
        return view('components.attendance-summary', ['p' => $p, 'l' => $l, 'a' => $a, 'e' => $e, 'total' => $p + $l + $a + $e,
            'rate' => AttendanceStats::rate($p, $l, $a, $e),
            'recent' => AttendanceRecord::with('supervisor.user')->where('student_id', $this->student->id)->orderByDesc('date')->limit(15)->get()]);
    }
}
