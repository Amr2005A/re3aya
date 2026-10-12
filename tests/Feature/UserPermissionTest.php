<?php
namespace Tests\Feature;
use App\Models\User;
use Database\Seeders\RiayaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class UserPermissionTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(RiayaSeeder::class); }
    private function userWith(array $perms = []): User { $u = User::factory()->create(['is_active' => true]); $u->givePermissionTo($perms); return $u; }
    public function test_guest_is_redirected_to_login(): void { $this->get('/users')->assertRedirect('/login'); }
    public function test_user_without_permission_gets_403_even_on_direct_route(): void {
        $u = $this->userWith(); $t = $this->userWith();
        $this->actingAs($u)->get('/users')->assertForbidden();
        $this->actingAs($u)->get(route('users.permissions.edit', $t))->assertForbidden();
        $this->actingAs($u)->put(route('users.permissions.update', $t), ['permissions' => ['users.view']])->assertForbidden();
    }
    public function test_user_with_permission_can_list(): void { $this->actingAs($this->userWith(['users.view']))->get('/users')->assertOk(); }
    public function test_cannot_grant_permission_you_do_not_have(): void {
        $a = $this->userWith(['users.manage-permissions', 'users.view']); $t = $this->userWith();
        $this->actingAs($a)->put(route('users.permissions.update', $t), ['permissions' => ['accounting.view']])->assertSessionHasErrors();
        $this->actingAs($a)->put(route('users.permissions.update', $t), ['permissions' => ['users.view']])->assertRedirect();
        $this->assertTrue($t->fresh()->can('users.view')); $this->assertFalse($t->fresh()->can('accounting.view'));
    }
    public function test_cannot_edit_own_permissions_or_owner(): void {
        $a = $this->userWith(['users.manage-permissions']); $owner = User::where('is_owner', true)->first();
        $this->actingAs($a)->get(route('users.permissions.edit', $a))->assertForbidden();
        $this->actingAs($a)->get(route('users.permissions.edit', $owner))->assertForbidden();
    }
    public function test_owner_can_manage_and_cannot_be_disabled(): void {
        $owner = User::where('is_owner', true)->first(); $t = $this->userWith();
        $this->actingAs($owner)->put(route('users.permissions.update', $t), ['permissions' => ['students.view-all']])->assertRedirect();
        $this->assertTrue($t->fresh()->can('students.view-all'));
        $this->actingAs($owner)->patch(route('users.toggle', $owner))->assertForbidden();
    }
    public function test_disabled_user_cannot_login(): void {
        User::factory()->create(['email' => 'x@y.test', 'password' => 'secret-pass', 'is_active' => false]);
        $this->post('/login', ['login' => 'x@y.test', 'password' => 'secret-pass'])->assertSessionHasErrors('login'); $this->assertGuest();
    }
}
