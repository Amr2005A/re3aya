<?php
namespace Tests\Feature;
use App\Models\User;
use Database\Seeders\RiayaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class LoginFlowTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(RiayaSeeder::class); }

    public function test_default_owner_can_login(): void {
        $this->post('/login', ['login' => 'amrmohissen.b@gmail.com', 'password' => '12345zob'])->assertRedirect(route('dashboard')); $this->assertAuthenticated();
        $this->get('/')->assertOk();
    }
    public function test_user_created_by_admin_can_login_and_never_gets_403_on_home(): void {
        $owner = User::where('is_owner', true)->first();
        $this->actingAs($owner)->post('/users', ['name' => 'جديد', 'email' => 'n@t.test', 'password' => 'password-1', 'password_confirmation' => 'password-1', 'is_active' => 1])->assertRedirect();
        auth()->logout();
        $this->post('/login', ['login' => 'n@t.test', 'password' => 'password-1'])->assertRedirect(); $this->assertAuthenticated();
        $this->get('/')->assertOk()->assertSee('لم تُمنح أي صلاحية');
    }
    public function test_user_without_dashboard_permission_is_redirected_to_first_allowed_section(): void {
        $u = User::factory()->create(['is_active' => true]); $u->givePermissionTo('students.view-own');
        $this->actingAs($u)->get('/')->assertRedirect(route('students.index'));
    }
    public function test_pending_user_gets_clear_message_only_with_correct_password(): void {
        User::factory()->create(['email' => 'p@t.test', 'password' => 'secret-pass', 'is_active' => false]);
        $this->post('/login', ['login' => 'p@t.test', 'password' => 'secret-pass'])->assertSessionHasErrors(['login' => 'حسابك بانتظار موافقة الإدارة أو معطّل. تواصل مع المسؤول.']);
        $this->post('/login', ['login' => 'p@t.test', 'password' => 'wrong'])->assertSessionHasErrors(['login' => 'بيانات الدخول غير صحيحة.']);
    }
    public function test_reseeding_does_not_reset_changed_owner_password(): void {
        $o = User::where('is_owner', true)->first(); $o->update(['password' => 'my-new-password']);
        $this->seed(RiayaSeeder::class);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('my-new-password', $o->fresh()->password));
        $this->assertSame(1, User::where('is_owner', true)->count());
    }
    public function test_seeder_promotes_existing_account_with_owner_email(): void {
        User::query()->delete();
        User::factory()->create(['email' => 'amrmohissen.b@gmail.com', 'password' => 'whatever-1', 'is_active' => false]);
        $this->seed(RiayaSeeder::class);
        $u = User::where('email', 'amrmohissen.b@gmail.com')->first();
        $this->assertTrue($u->is_owner && $u->is_active); $this->assertSame(1, User::count());
        $this->post('/login', ['login' => 'amrmohissen.b@gmail.com', 'password' => '12345zob'])->assertRedirect(); $this->assertAuthenticated();
    }
}
