<?php
namespace Database\Seeders;
use App\Models\{User, WorkingDay, Setting};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class RiayaSeeder extends Seeder {
    public function run(): void {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        collect(config('riaya-permissions'))->flatMap(fn ($g) => array_keys($g['items']))
            ->each(fn ($p) => Permission::findOrCreate($p, 'web'));
        foreach ([1, 4] as $d) WorkingDay::firstOrCreate(['weekday' => $d], ['is_active' => true]);
        Setting::firstOrCreate(['key' => 'points.allow_negative'], ['group' => 'points', 'value' => false]);
        Setting::firstOrCreate(['key' => 'points.max_per_award'], ['group' => 'points', 'value' => 100]);
        Setting::firstOrCreate(['key' => 'project.name'], ['group' => 'general', 'value' => 'رعاية']);
        Setting::firstOrCreate(['key' => 'finance.currency'], ['group' => 'general', 'value' => '']);
        $cfg = config('riaya.owner');
        $o = User::where('email', $cfg['email'])->first() ?? User::where('email', 'owner@riaya.test')->first();
        $ready = $o && $o->is_owner && $o->email === $cfg['email'];
        if ($ready) {
            // المالك موجود: لا نلمس كلمة مرور غيّرها صاحبها
        } elseif ($o) { // حساب موجود (مسجَّل سابقاً أو الافتراضي القديم) يُرقّى إلى مالك
            $o->forceFill(['email' => $cfg['email'], 'password' => $cfg['password'], 'is_owner' => true, 'is_active' => true])->save();
        } elseif (! User::where('is_owner', true)->exists()) {
            (new User())->forceFill(['name' => $cfg['name'], 'email' => $cfg['email'], 'password' => $cfg['password'], 'is_owner' => true, 'is_active' => true])->save();
        }
    }
}
