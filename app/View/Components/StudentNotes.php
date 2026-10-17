<?php
namespace App\View\Components;
use App\Models\{Note, Student};
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Component;
class StudentNotes extends Component {
    public function __construct(public Student $student) {}
    public function render() {
        return view('components.student-notes', [
            'notes' => Note::with('author')->where('student_id', $this->student->id)->latest()->limit(50)->get(),
            'total' => Note::where('student_id', $this->student->id)->count(),
            'canCreate' => Gate::allows('create', [Note::class, $this->student]),
        ]);
    }
}
