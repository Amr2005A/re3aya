<?php
namespace Tests\Feature;
use App\Models\{Note, Student, Supervisor, User};
use Database\Seeders\RiayaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class NoteTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(RiayaSeeder::class); }
    private function sup(array $perms, string $name = 'مشرف'): array { $u = User::factory()->create(['name' => $name]); $s = Supervisor::factory()->create(['user_id' => $u->id]); $u->givePermissionTo($perms); return [$u, $s]; }
    private function note(Student $st, User $author, string $body): Note { $n = new Note(['student_id' => $st->id, 'body' => $body]); $n->author_id = $author->id; $n->save(); return $n; }
    private array $own = ['notes.create', 'notes.view-own', 'notes.update-own'];

    public function test_supervisor_adds_note_and_author_is_always_the_current_user(): void {
        [$u, $s] = $this->sup($this->own); $st = Student::factory()->create(['supervisor_id' => $s->id]); [$other] = $this->sup([]);
        $this->actingAs($u)->post(route('students.notes.store', $st), ['body' => 'ملاحظة جديدة', 'author_id' => $other->id])->assertRedirect();
        $this->assertDatabaseHas('notes', ['student_id' => $st->id, 'author_id' => $u->id, 'body' => 'ملاحظة جديدة']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'note.created']);
    }
    public function test_cannot_add_note_to_another_supervisors_student(): void {
        [$u] = $this->sup($this->own); [, $o] = $this->sup([]); $st = Student::factory()->create(['supervisor_id' => $o->id]);
        $this->actingAs($u)->post(route('students.notes.store', $st), ['body' => 'محاولة'])->assertForbidden(); $this->assertSame(0, Note::count());
    }
    public function test_create_requires_the_create_permission_and_visibility(): void {
        [$a, $sa] = $this->sup(['notes.view-own']); $st = Student::factory()->create(['supervisor_id' => $sa->id]);
        $this->actingAs($a)->post(route('students.notes.store', $st), ['body' => 'بدون صلاحية إضافة'])->assertForbidden();
        [$b, $sb] = $this->sup(['notes.create']); $st2 = Student::factory()->create(['supervisor_id' => $sb->id]);
        $this->actingAs($b)->post(route('students.notes.store', $st2), ['body' => 'بدون صلاحية عرض'])->assertForbidden();
    }
    public function test_body_is_validated(): void {
        [$u, $s] = $this->sup($this->own); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->actingAs($u)->post(route('students.notes.store', $st), ['body' => ''])->assertSessionHasErrors('body');
        $this->actingAs($u)->post(route('students.notes.store', $st), ['body' => str_repeat('ا', 5001)])->assertSessionHasErrors('body');
    }
    public function test_supervisor_sees_only_notes_of_own_students(): void {
        [$u, $s] = $this->sup($this->own); [$o, $os] = $this->sup([], 'مشرف آخر');
        $mine = Student::factory()->create(['supervisor_id' => $s->id]); $theirs = Student::factory()->create(['supervisor_id' => $os->id]);
        $this->note($mine, $u, 'ملاحظة ظاهرة'); $this->note($theirs, $o, 'ملاحظة مخفية');
        $this->actingAs($u)->get('/notes')->assertOk()->assertSee('ملاحظة ظاهرة')->assertDontSee('ملاحظة مخفية');
    }
    public function test_view_all_sees_everything_with_author_names(): void {
        [$u, $s] = $this->sup($this->own, 'مشرف أحمد'); [$o, $os] = $this->sup($this->own, 'مشرف محمد');
        $this->note(Student::factory()->create(['supervisor_id' => $s->id]), $u, 'نص أول'); $this->note(Student::factory()->create(['supervisor_id' => $os->id]), $o, 'نص ثانٍ');
        [$admin] = $this->sup(['notes.view-all']);
        $this->actingAs($admin)->get('/notes')->assertOk()->assertSee('نص أول')->assertSee('نص ثانٍ')->assertSee('مشرف أحمد')->assertSee('مشرف محمد');
    }
    public function test_student_profile_shows_notes_with_author_only_to_allowed_users(): void {
        [$u, $s] = $this->sup([...$this->own, 'students.view-own'], 'مشرف الطالب'); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->note($st, $u, 'ملاحظة في الملف');
        $this->actingAs($u)->get(route('students.show', $st))->assertOk()->assertSee('ملاحظة في الملف')->assertSee('مشرف الطالب');
        [$x] = $this->sup(['students.view-all']);
        $this->actingAs($x)->get(route('students.show', $st))->assertOk()->assertDontSee('ملاحظة في الملف');
    }
    public function test_only_author_can_edit_and_edit_is_audited(): void {
        [$u, $s] = $this->sup($this->own); $st = Student::factory()->create(['supervisor_id' => $s->id]); $n = $this->note($st, $u, 'النص القديم');
        $peer2 = User::factory()->create(); $peer2->givePermissionTo(['notes.update-own', 'notes.view-all']);
        $this->actingAs($peer2)->put(route('notes.update', $n), ['body' => 'محاولة تعديل'])->assertForbidden();
        $this->actingAs($u)->put(route('notes.update', $n), ['body' => 'النص الجديد'])->assertRedirect();
        $this->assertSame('النص الجديد', $n->fresh()->body);
        $this->assertDatabaseHas('audit_logs', ['action' => 'note.updated', 'auditable_id' => $n->id]);
    }
    public function test_delete_is_soft_and_needs_permission(): void {
        [$u, $s] = $this->sup([...$this->own, 'notes.delete']); [$w, $ws] = $this->sup($this->own);
        $st = Student::factory()->create(['supervisor_id' => $s->id]); $n = $this->note($st, $u, 'للحذف');
        $this->actingAs($w)->delete(route('notes.destroy', $n))->assertForbidden();
        $this->actingAs($u)->delete(route('notes.destroy', $n))->assertRedirect();
        $this->assertSoftDeleted('notes', ['id' => $n->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'note.deleted', 'auditable_id' => $n->id]);
        $this->actingAs($u)->get('/notes')->assertDontSee('للحذف');
    }
    public function test_guest_and_unprivileged_are_blocked(): void {
        $this->get('/notes')->assertRedirect('/login');
        $plain = User::factory()->create(); $this->actingAs($plain)->get('/notes')->assertForbidden();
    }
}
