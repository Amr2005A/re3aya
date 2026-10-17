<?php
namespace App\Actions;
use App\Models\{Note, Student, User};
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class SaveNote {
    public function __invoke(User $actor, string $body, ?Student $student = null, ?Note $note = null): Note {
        return DB::transaction(function () use ($actor, $body, $student, $note) {
            $body = trim($body);
            if ($note) {
                $old = $note->body; $note->update(['body' => $body]);
                if ($old !== $body) AuditLogger::log('note.updated', $note, ['body' => $old], ['body' => $body]);
                return $note;
            }
            $note = new Note(['student_id' => $student->id, 'body' => $body]);
            $note->author_id = $actor->id; // دائماً المستخدم الحالي، لا من الطلب
            $note->save();
            AuditLogger::log('note.created', $note, null, ['student_id' => $student->id]);
            return $note;
        });
    }
}
