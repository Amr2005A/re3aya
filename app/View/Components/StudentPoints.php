<?php
namespace App\View\Components;
use App\Models\{Circle, PointTransaction, Student};
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Component;
class StudentPoints extends Component {
    public function __construct(public Student $student) {}
    public function render() {
        $id = $this->student->id;
        return view('components.student-points', [
            'rows' => PointTransaction::with(['student', 'circle', 'criterion', 'awarder', 'reversedBy'])->where('student_id', $id)->orderByDesc('occurred_at')->orderByDesc('id')->limit(30)->get(),
            'recent' => (int) PointTransaction::where('student_id', $id)->where('occurred_at', '>=', now()->subDays(30))->sum('points'),
            'canAward' => Gate::allows('award', [PointTransaction::class, $this->student]),
            'circles' => Circle::where('is_active', true)->with('activeCriteria')->orderBy('sort_order')->get(),
        ]);
    }
}
