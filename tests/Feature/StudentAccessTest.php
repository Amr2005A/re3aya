<?php
namespace Tests\Feature;
use App\Models\{Student, Supervisor, User};
use App\Services\PhoneService;
use Database\Seeders\RiayaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class StudentAccessTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(RiayaSeeder::class); }
    private function sup(array $perms): array { $u = User::factory()->create(); $s = Supervisor::factory()->create(['user_id' => $u->id]); $u->givePermissionTo($perms); return [$u, $s]; }
    public function test_local_number_uses_configured_country_code(): void {
        config(['riaya.country_code' => '963']); $this->assertSame('https://wa.me/963912345678', PhoneService::whatsappUrl('0912 345 678'));
        config(['riaya.country_code' => '']); $this->assertNull(PhoneService::whatsappUrl('0912345678'));
    }
    public function test_supervisor_sees_only_own_students_and_direct_urls_are_blocked(): void {
        [$u, $s] = $this->sup(['students.view-own', 'students.update']); [, $other] = $this->sup([]);
        $mine = Student::factory()->create(['supervisor_id' => $s->id, 'name' => 'طالب أول']); $theirs = Student::factory()->create(['supervisor_id' => $other->id, 'name' => 'طالب آخر']);
        $this->actingAs($u)->get('/students')->assertOk()->assertSee('طالب أول')->assertDontSee('طالب آخر');
        $this->actingAs($u)->get(route('students.show', $theirs))->assertForbidden();
        $this->actingAs($u)->put(route('students.update', $theirs), ['name' => 'x', 'phone' => '0912345678', 'status' => 'active'])->assertForbidden();
        $this->actingAs($u)->get(route('students.show', $mine))->assertOk();
    }
    public function test_view_own_creator_is_forced_as_supervisor_of_new_student(): void {
        [$u, $s] = $this->sup(['students.view-own', 'students.create']); [, $other] = $this->sup([]);
        $this->actingAs($u)->post('/students', ['name' => 'جديد', 'phone' => '0912345678', 'status' => 'active', 'supervisor_id' => $other->id])->assertRedirect();
        $this->assertSame($s->id, Student::where('name', 'جديد')->first()->supervisor_id);
    }
    public function test_phone_is_required(): void {
        $owner = User::where('is_owner', true)->first();
        $this->actingAs($owner)->post('/students', ['name' => 'بلا هاتف', 'status' => 'active'])->assertSessionHasErrors('phone');
    }
    public function test_transfer_requires_permission_and_writes_history(): void {
        [$plain] = $this->sup([]); [, $a] = $this->sup([]); [, $b] = $this->sup([]);
        $st = Student::factory()->create(['supervisor_id' => $a->id]);
        $this->actingAs($plain)->post('/assignments/transfer', ['student_ids' => [$st->id], 'to_supervisor_id' => $b->id])->assertForbidden();
        $owner = User::where('is_owner', true)->first();
        $this->actingAs($owner)->post('/assignments/transfer', ['student_ids' => [$st->id], 'to_supervisor_id' => $b->id, 'reason' => 'اختبار'])->assertRedirect();
        $this->assertSame($b->id, $st->fresh()->supervisor_id);
        $this->assertDatabaseHas('student_supervisor_history', ['student_id' => $st->id, 'from_supervisor_id' => $a->id, 'to_supervisor_id' => $b->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'student.transferred', 'auditable_id' => $st->id]);
    }
    public function test_new_supervisor_gets_template_limited_to_grantor_permissions(): void {
        $g = User::factory()->create(); $g->givePermissionTo(['supervisors.create', 'students.view-own']);
        $this->actingAs($g)->post('/supervisors', ['name' => 'م', 'email' => 'm@t.test', 'password' => 'password-1', 'password_confirmation' => 'password-1'])->assertRedirect();
        $new = User::where('email', 'm@t.test')->first();
        $this->assertTrue($new->can('students.view-own')); $this->assertFalse($new->can('points.award'));
    }
}
