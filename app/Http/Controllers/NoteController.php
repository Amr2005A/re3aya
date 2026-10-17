<?php
namespace App\Http\Controllers;
use App\Actions\{DeleteNote, SaveNote};
use App\Http\Requests\SaveNoteRequest;
use App\Models\{Note, Student, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class NoteController extends Controller {
    public function index(Request $r) {
        Gate::authorize('viewAny', Note::class);
        $r->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);
        $u = $r->user();
        $base = Note::visibleTo($u);
        $notes = (clone $base)->with(['student', 'author'])
            ->when($r->q, fn ($q, $s) => $q->where('body', 'like', "%$s%"))
            ->when($r->student, fn ($q, $v) => $q->where('student_id', $v))
            ->when($r->author, fn ($q, $v) => $q->where('author_id', $v))
            ->when($r->from, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($r->to, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()->paginate(15)->withQueryString();
        $authors = User::whereIn('id', (clone $base)->select('author_id'))->orderBy('name')->get(['id', 'name']);
        return view('notes.index', compact('notes', 'authors'));
    }
    public function store(SaveNoteRequest $r, Student $student, SaveNote $save) {
        Gate::authorize('create', [Note::class, $student]);
        $save($r->user(), $r->validated('body'), $student);
        return redirect()->to(route('students.show', $student) . '#notes')->with('success', 'تمت إضافة الملاحظة.');
    }
    public function update(SaveNoteRequest $r, Note $note, SaveNote $save) {
        Gate::authorize('update', $note);
        $save($r->user(), $r->validated('body'), null, $note);
        return redirect()->to(route('students.show', $note->student_id) . '#notes')->with('success', 'تم تعديل الملاحظة.');
    }
    public function destroy(Note $note, DeleteNote $delete) {
        Gate::authorize('delete', $note);
        $delete($note);
        return back()->with('success', 'تم حذف الملاحظة (حذفاً ناعماً، وتبقى محفوظة في السجل).');
    }
}
