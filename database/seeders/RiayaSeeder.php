<?php
namespace Database\Seeders;
use App\Models\{User, WorkingDay, Setting};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class RiayaSeeder extends Seeder {
    public function run(): void {
        collect(config('riaya-permissions'))->flatMap(fn ($g) => array_keys($g['items']))
            ->each(fn ($p) => Permission::findOrCreate($p, 'web'));
        foreach ([1, 4] as $d) WorkingDay::firstOrCreate(['weekday' => $d], ['is_active' => true]);
        Setting::firstOrCreate(['key' => 'project.name'], ['group' => 'general', 'value' => 'رعاية']);
        Setting::firstOrCreate(['key' => 'finance.currency'], ['group' => 'general', 'value' => '']);
        $o = User::firstOrNew(['email' => 'owner@riaya.test']);
        if (! $o->exists) { $o->name = 'المالك'; $o->password = 'change-me-now'; $o->is_owner = true; $o->save(); }
    }
}
