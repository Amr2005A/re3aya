<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('finance_categories', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('type', 10)->index();
            $t->boolean('is_active')->default(true); $t->timestamps();
        });
        Schema::create('finance_transactions', function (Blueprint $t) {
            $t->id(); $t->string('type', 10); // income|expense
            $t->foreignId('category_id')->nullable()->constrained('finance_categories')->nullOnDelete();
            $t->decimal('amount', 14, 2); $t->date('occurred_on')->index();
            $t->string('description'); $t->text('notes')->nullable();
            $t->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('reverses_id')->nullable()->unique()->constrained('finance_transactions')->nullOnDelete();
            $t->timestamps(); $t->index(['type','occurred_on']);
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary(); $t->string('group')->default('general')->index();
            $t->json('value')->nullable(); $t->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 60)->index(); $t->nullableMorphs('auditable');
            $t->json('old_values')->nullable(); $t->json('new_values')->nullable();
            $t->string('ip', 45)->nullable(); $t->timestamp('created_at')->useCurrent()->index();
        });
    }
    public function down(): void {
        foreach (['audit_logs','settings','finance_transactions','finance_categories'] as $x) Schema::dropIfExists($x);
    }
};
