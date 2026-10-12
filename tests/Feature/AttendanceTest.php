<?php
namespace Tests\Feature;
use App\Models\{AttendanceRecord, Student, Supervisor, User, WorkingDay};
use Carbon\Carbon;
use Database\Seeders\RiayaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AttendanceTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(RiayaSeeder::class); } // الاثنين والخميس
    private function sup(array $perms): array { $u = User::factory()->create(); $s = Supervisor::factory()->create(['user_id' => $u->id]); $u->givePermissionTo($perms); return [$u, $s]; }
    private function monday(): string { return Carbon::now()->previous(Carbon::MONDAY)->toDateString(); }
    private function post_(User $u, string $date, array $rows) { return $this->actingAs($u)->post('/attendance', ['date' => $date, 'rows' => $rows]); }

    public function test_past_working_day_can_be_recorded_with_supervisor_snapshot(): void {
        [$u, $s] = $this->sup(['attendance.record']); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->post_($u, $this->monday(), [$st->id => ['status' => 'present']])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('attendance_records', ['student_id' => $st->id, 'status' => 'present', 'supervisor_id' => $s->id, 'recorded_by' => $u->id]);
    }
    public function test_future_date_and_non_working_day_are_rejected(): void {
        [$u, $s] = $this->sup(['attendance.record']); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->post_($u, today()->addDay()->toDateString(), [$st->id => ['status' => 'present']])->assertSessionHasErrors('date');
        $tuesday = Carbon::now()->previous(Carbon::TUESDAY)->toDateString();
        $this->post_($u, $tuesday, [$st->id => ['status' => 'present']])->assertSessionHasErrors('date');
        $this->assertSame(0, AttendanceRecord::count());
    }
    public function test_cannot_record_for_another_supervisors_student(): void {
        [$u] = $this->sup(['attendance.record']); [, $other] = $this->sup([]); $st = Student::factory()->create(['supervisor_id' => $other->id]);
        $this->post_($u, $this->monday(), [$st->id => ['status' => 'present']]);
        $this->assertSame(0, AttendanceRecord::count());
    }
    public function test_editing_existing_record_requires_update_permission(): void {
        [$u, $s] = $this->sup(['attendance.record']); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->post_($u, $this->monday(), [$st->id => ['status' => 'present']]);
        $this->post_($u, $this->monday(), [$st->id => ['status' => 'absent']]);
        $this->assertSame('present', AttendanceRecord::first()->status->value);
        $u->givePermissionTo('attendance.update');
        $this->post_($u->fresh(), $this->monday(), [$st->id => ['status' => 'absent']]);
        $this->assertSame('absent', AttendanceRecord::first()->status->value);
        $this->assertDatabaseHas('audit_logs', ['action' => 'attendance.updated']);
    }
    public function test_changing_working_days_keeps_old_records_and_their_date_editable(): void {
        [$u, $s] = $this->sup(['attendance.record', 'attendance.update']); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->post_($u, $this->monday(), [$st->id => ['status' => 'present']]);
        $owner = User::where('is_owner', true)->first();
        $this->actingAs($owner)->put('/settings/working-days', ['days' => [4]])->assertSessionHasNoErrors();
        $this->assertFalse((bool) WorkingDay::where('weekday', 1)->value('is_active'));
        $this->assertSame(1, AttendanceRecord::count()); $this->assertSame('present', AttendanceRecord::first()->status->value);
        $this->post_($u, $this->monday(), [$st->id => ['status' => 'late']])->assertSessionHasNoErrors();
        $this->assertSame('late', AttendanceRecord::first()->status->value);
    }
    public function test_working_days_require_at_least_one_and_permission(): void {
        $owner = User::where('is_owner', true)->first(); [$plain] = $this->sup([]);
        $this->actingAs($owner)->put('/settings/working-days', ['days' => []])->assertSessionHasErrors('days');
        $this->actingAs($plain)->put('/settings/working-days', ['days' => [1]])->assertForbidden();
    }
    public function test_pages_are_forbidden_without_permission(): void {
        [$plain] = $this->sup([]);
        foreach (['/attendance', '/attendance/history', '/attendance/report'] as $url) $this->actingAs($plain)->get($url)->assertForbidden();
    }
    public function test_history_is_limited_to_own_students(): void {
        [$u, $s] = $this->sup(['attendance.view-own']); [, $o] = $this->sup([]);
        $mine = Student::factory()->create(['supervisor_id' => $s->id, 'name' => 'ظاهر']); $theirs = Student::factory()->create(['supervisor_id' => $o->id, 'name' => 'مخفي']);
        foreach ([$mine, $theirs] as $x) AttendanceRecord::create(['student_id' => $x->id, 'date' => $this->monday(), 'status' => 'present', 'supervisor_id' => $x->supervisor_id]);
        $this->actingAs($u)->get('/attendance/history')->assertOk()->assertSee('ظاهر')->assertDontSee('مخفي');
    }
}
