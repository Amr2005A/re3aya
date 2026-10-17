<?php
namespace Tests\Feature;
use App\Enums\PointTransactionType;
use App\Models\{Circle, CircleCriterion, PointTransaction, Student, Supervisor, User};
use App\Services\SettingsService;
use Database\Seeders\RiayaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PointsTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(RiayaSeeder::class); }
    private function sup(array $perms, string $name = 'مشرف'): array { $u = User::factory()->create(['name' => $name]); $s = Supervisor::factory()->create(['user_id' => $u->id]); $u->givePermissionTo($perms); return [$u, $s]; }
    private function circle(string $name = 'حلقة القرآن'): Circle { return Circle::create(['name' => $name, 'is_active' => true, 'sort_order' => 0]); }
    private function crit(Circle $c, int $pts = 5, bool $active = true): CircleCriterion { return $c->criteria()->create(['name' => 'المشاركة', 'default_points' => $pts, 'sort_order' => 1, 'is_active' => $active]); }
    private array $award = ['points.award', 'points.view'];
    private function give(User $u, Student $st, int $pts, string $why = 'سبب'): void { $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'points' => $pts, 'reason' => $why])->assertSessionHasNoErrors(); }

    public function test_award_updates_balance_ledger_and_audit(): void {
        [$u, $s] = $this->sup($this->award); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'points' => 10, 'reason' => 'حضور الحلقة', 'note' => 'ممتاز'])->assertRedirect();
        $this->assertSame(10, $st->fresh()->points_balance);
        $this->assertDatabaseHas('point_transactions', ['student_id' => $st->id, 'points' => 10, 'type' => 'award', 'awarded_by' => $u->id, 'reason' => 'حضور الحلقة']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'points.awarded']);
    }
    public function test_criterion_supplies_reason_and_circle(): void {
        [$u, $s] = $this->sup($this->award); $st = Student::factory()->create(['supervisor_id' => $s->id]); $c = $this->circle(); $k = $this->crit($c);
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'criteria_id' => $k->id, 'points' => 5])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('point_transactions', ['student_id' => $st->id, 'reason' => 'المشاركة', 'circle_id' => $c->id, 'criteria_id' => $k->id]);
    }
    public function test_cannot_award_without_permission_or_for_other_supervisors_student(): void {
        [$u, $s] = $this->sup($this->award); [, $o] = $this->sup([]); $theirs = Student::factory()->create(['supervisor_id' => $o->id]);
        $this->actingAs($u)->post('/points', ['student_id' => $theirs->id, 'points' => 5, 'reason' => 'x'])->assertForbidden();
        [$viewer, $vs] = $this->sup(['points.view']); $mine = Student::factory()->create(['supervisor_id' => $vs->id]);
        $this->actingAs($viewer)->post('/points', ['student_id' => $mine->id, 'points' => 5, 'reason' => 'x'])->assertForbidden();
        $this->assertSame(0, PointTransaction::count());
    }
    public function test_points_are_validated(): void {
        [$u, $s] = $this->sup($this->award); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        foreach ([0, 101, 'abc'] as $bad) $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'points' => $bad, 'reason' => 'x'])->assertSessionHasErrors('points');
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'points' => 5])->assertSessionHasErrors('reason');
        $this->assertSame(0, $st->fresh()->points_balance);
    }
    public function test_negative_points_blocked_by_default_and_allowed_when_enabled(): void {
        [$u, $s] = $this->sup($this->award); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'points' => -5, 'reason' => 'سلوك'])->assertSessionHasErrors('points');
        SettingsService::put('points.allow_negative', true, 'points');
        $this->give($u, $st, -5, 'سلوك'); $this->assertSame(-5, $st->fresh()->points_balance);
    }
    public function test_inactive_criterion_and_foreign_circle_are_rejected(): void {
        [$u, $s] = $this->sup($this->award); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $c1 = $this->circle('أ'); $c2 = $this->circle('ب'); $off = $this->crit($c1, 5, false); $on = $this->crit($c1);
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'criteria_id' => $off->id, 'points' => 5])->assertSessionHasErrors('criteria_id');
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'circle_id' => $c2->id, 'criteria_id' => $on->id, 'points' => 5])->assertSessionHasErrors('criteria_id');
    }
    public function test_ledger_is_limited_to_managed_students(): void {
        [$u, $s] = $this->sup($this->award); [$o, $os] = $this->sup($this->award);
        $mine = Student::factory()->create(['supervisor_id' => $s->id]); $theirs = Student::factory()->create(['supervisor_id' => $os->id]);
        $this->give($u, $mine, 5, 'سبب ظاهر'); $this->give($o, $theirs, 5, 'سبب مخفي');
        $this->actingAs($u)->get('/points')->assertOk()->assertSee('سبب ظاهر')->assertDontSee('سبب مخفي');
        [$all] = $this->sup(['points.view', 'students.view-all']);
        $this->actingAs($all)->get('/points')->assertSee('سبب ظاهر')->assertSee('سبب مخفي');
    }
    public function test_reverse_creates_opposite_entry_only_once(): void {
        [$u, $s] = $this->sup([...$this->award, 'points.reverse']); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->give($u, $st, 10); $tx = PointTransaction::first();
        $this->actingAs($u)->post(route('points.reverse', $tx))->assertRedirect();
        $this->assertSame(0, $st->fresh()->points_balance);
        $this->assertDatabaseHas('point_transactions', ['reverses_id' => $tx->id, 'points' => -10, 'type' => 'reversal']);
        $this->actingAs($u)->post(route('points.reverse', $tx))->assertSessionHasErrors('reverse');
        $rev = PointTransaction::where('reverses_id', $tx->id)->first();
        $this->actingAs($u)->post(route('points.reverse', $rev))->assertSessionHasErrors('reverse');
        $this->assertSame(2, PointTransaction::count()); $this->assertSame(0, $st->fresh()->points_balance);
    }
    public function test_reverse_needs_permission_and_scope_and_skips_badge_entries(): void {
        [$u, $s] = $this->sup($this->award); [$r, $rs] = $this->sup([...$this->award, 'points.reverse']);
        $st = Student::factory()->create(['supervisor_id' => $s->id]); $this->give($u, $st, 10); $tx = PointTransaction::first();
        $this->actingAs($u)->post(route('points.reverse', $tx))->assertForbidden();
        $this->actingAs($r)->post(route('points.reverse', $tx))->assertForbidden(); // ليس من طلابه
        $mine = Student::factory()->create(['supervisor_id' => $rs->id]);
        $badge = PointTransaction::create(['student_id' => $mine->id, 'points' => 50, 'type' => PointTransactionType::Badge, 'reason' => 'وسام', 'occurred_at' => now()]);
        $this->actingAs($r)->post(route('points.reverse', $badge))->assertSessionHasErrors('reverse');
    }
    public function test_balance_equals_ledger_sum_and_reconcile_command_detects_and_fixes_drift(): void {
        [$u, $s] = $this->sup([...$this->award, 'points.reverse']); $st = Student::factory()->create(['supervisor_id' => $s->id]);
        $this->give($u, $st, 10); $this->give($u, $st, 7); $this->actingAs($u)->post(route('points.reverse', PointTransaction::first()));
        $this->assertSame((int) PointTransaction::where('student_id', $st->id)->sum('points'), $st->fresh()->points_balance);
        $this->artisan('riaya:reconcile-points')->assertExitCode(0);
        Student::whereKey($st->id)->update(['points_balance' => 999]);
        $this->artisan('riaya:reconcile-points')->assertExitCode(1);
        $this->artisan('riaya:reconcile-points', ['--fix' => true])->assertExitCode(0);
        $this->assertSame(7, $st->fresh()->points_balance); $this->assertDatabaseHas('audit_logs', ['action' => 'points.reconciled']);
    }
    public function test_circle_and_criteria_management_require_permissions(): void {
        [$none] = $this->sup([]); $this->actingAs($none)->get('/circles')->assertForbidden();
        [$mgr] = $this->sup(['circles.view', 'circles.create']);
        $this->actingAs($mgr)->post('/circles', ['name' => 'حلقة جديدة', 'is_active' => 1])->assertRedirect(); $c = Circle::where('name', 'حلقة جديدة')->first(); $this->assertNotNull($c);
        $this->actingAs($mgr)->put(route('circles.update', $c), ['name' => 'تغيير', 'is_active' => 1])->assertForbidden();
        $this->actingAs($mgr)->post(route('circles.criteria.store', $c), ['name' => 'بند', 'default_points' => 5])->assertForbidden();
        [$crit] = $this->sup(['circles.view', 'circles.manage-criteria']);
        $this->actingAs($crit)->post(route('circles.criteria.store', $c), ['name' => 'بند', 'default_points' => 5])->assertSessionHasNoErrors();
        $this->actingAs($crit)->post(route('circles.criteria.store', $c), ['name' => 'خصم', 'default_points' => -5])->assertSessionHasErrors('default_points');
        $this->assertSame(1, $c->criteria()->count());
    }
    public function test_deleting_circle_soft_deletes_criteria_but_keeps_history_readable(): void {
        [$u, $s] = $this->sup($this->award); $st = Student::factory()->create(['supervisor_id' => $s->id]); $c = $this->circle('حلقة قديمة'); $k = $this->crit($c);
        $this->actingAs($u)->post('/points', ['student_id' => $st->id, 'criteria_id' => $k->id, 'points' => 5]);
        [$d] = $this->sup(['circles.delete']); $this->actingAs($d)->delete(route('circles.destroy', $c))->assertRedirect();
        $this->assertSoftDeleted('circles', ['id' => $c->id]); $this->assertSoftDeleted('circle_criteria', ['id' => $k->id]);
        $this->assertSame(1, PointTransaction::where('circle_id', $c->id)->count());
        $this->actingAs($u)->get('/points')->assertOk()->assertSee('حلقة قديمة');
    }
    public function test_points_settings_permissions_and_saving(): void {
        [$plain] = $this->sup([]); $this->actingAs($plain)->put('/settings/points', ['max_per_award' => 50])->assertForbidden();
        $owner = User::where('is_owner', true)->first();
        $this->actingAs($owner)->put('/settings/points', ['max_per_award' => 50, 'allow_negative' => 1])->assertSessionHasNoErrors();
        $this->assertSame(50, SettingsService::maxPointsPerAward()); $this->assertTrue(SettingsService::allowNegativePoints());
    }
    public function test_student_profile_points_tab_visibility(): void {
        [$u, $s] = $this->sup([...$this->award, 'students.view-own']); $st = Student::factory()->create(['supervisor_id' => $s->id]); $this->give($u, $st, 12, 'نقاط الملف');
        $this->actingAs($u)->get(route('students.show', $st))->assertOk()->assertSee('نقاط الملف')->assertSee('الرصيد الحالي');
        [$x] = $this->sup(['students.view-all']); $this->actingAs($x)->get(route('students.show', $st))->assertOk()->assertDontSee('نقاط الملف');
    }
    public function test_guest_is_redirected(): void { $this->get('/points')->assertRedirect('/login'); $this->get('/circles')->assertRedirect('/login'); }
}
