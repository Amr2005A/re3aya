<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->nullable()->unique()->after('name');
            $t->boolean('is_owner')->default(false)->after('password');
            $t->boolean('is_active')->default(true)->index();
            $t->timestamp('last_login_at')->nullable();
            $t->softDeletes();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['username','is_owner','is_active','last_login_at','deleted_at']);
        });
    }
};
