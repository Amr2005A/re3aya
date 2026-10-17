<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
class RegistrationAndSeedTest extends TestCase {
    use RefreshDatabase;
    private array $valid = ['name' => 'مستخدم جديد', 'email' => 'new@t.test', 'password' => 'password-123', 'password_confirmation' => 'password-123'];

    public function test_default_db_seed_creates_owner_and_permissions(): void {
        $this->seed(); // DatabaseSeeder
        $this->assertTrue(User::where('email', config('riaya.owner.email'))->value('is_owner') == true);
        $this->assertGreaterThan(40, Permission::count());
        $this->assertDatabaseHas('working_days', ['weekday' => 1, 'is_active' => true]);
    }
    public function test_register_page_loads(): void { $this->get('/register')->assertOk()->assertSee('طلب حساب جديد'); }
    public function test_registration_creates_inactive_user_without_permissions(): void {
        $this->post('/register', $this->valid)->assertRedirect('/login');
        $u = User::where('email', 'new@t.test')->first();
        $this->assertFalse($u->is_active); $this->assertFalse($u->is_owner); $this->assertCount(0, $u->getAllPermissions());
        $this->assertDatabaseHas('audit_logs', ['action' => 'auth.registered', 'auditable_id' => $u->id]);
    }
    public function test_pending_user_cannot_login_until_activated(): void {
        $this->post('/register', $this->valid);
        $this->post('/login', ['login' => 'new@t.test', 'password' => 'password-123'])->assertSessionHasErrors('login'); $this->assertGuest();
        User::where('email', 'new@t.test')->update(['is_active' => true]);
        $this->post('/login', ['login' => 'new@t.test', 'password' => 'password-123'])->assertRedirect(); $this->assertAuthenticated();
    }
    public function test_cannot_self_assign_owner_or_activate_via_payload(): void {
        $this->post('/register', $this->valid + ['is_owner' => 1, 'is_active' => 1]);
        $u = User::where('email', 'new@t.test')->first(); $this->assertFalse($u->is_owner); $this->assertFalse($u->is_active);
    }
    public function test_validation_and_honeypot(): void {
        $this->post('/register', ['name' => '', 'email' => 'x'] )->assertSessionHasErrors(['name', 'email', 'password']);
        $this->post('/register', $this->valid + ['website' => 'spam'])->assertRedirect('/login');
        $this->assertSame(0, User::where('email', 'new@t.test')->count());
        User::factory()->create(['email' => 'dup@t.test']);
        $this->post('/register', ['email' => 'dup@t.test'] + $this->valid)->assertSessionHasErrors('email');
    }
    public function test_registration_can_be_disabled(): void {
        config(['riaya.registration_enabled' => false]);
        $this->get('/register')->assertNotFound(); $this->post('/register', $this->valid)->assertNotFound();
    }
}
