<?php
namespace App\Actions;
use App\Models\Note;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
class DeleteNote {
    /** حذف ناعم: تبقى الملاحظة في قاعدة البيانات ويحفظ السجل نصها */
    public function __invoke(Note $note): void {
        DB::transaction(function () use ($note) {
            AuditLogger::log('note.deleted', $note, ['body' => $note->body, 'author_id' => $note->author_id], null);
            $note->delete();
        });
    }
}
