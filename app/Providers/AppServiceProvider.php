<?php
namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
    public function boot(): void {
        // المالك يتجاوز فحوص الصلاحيات، عدا قواعد حماية المالك والحساب الذاتي في الـ Policy
        Gate::before(fn ($u, $ability) => $u->is_owner && ! in_array($ability, ['disable', 'managePermissions']) ? true : null);
    }
}
