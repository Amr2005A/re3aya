<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('supervisors', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->unique()->constrained()->restrictOnDelete();
            $t->string('phone', 30)->nullable(); $t->boolean('is_active')->default(true)->index();
            $t->timestamps(); $t->softDeletes();
        });
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->string('name')->index(); $t->string('father_name')->nullable();
            $t->string('address')->nullable();
            $t->string('phone', 30); $t->string('phone_extra', 30)->nullable();
            $t->string('father_job')->nullable();
            $t->foreignId('grade_level_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('supervisor_id')->nullable()->constrained()->nullOnDelete();
            $t->string('status', 20)->default('active')->index();
            $t->date('registered_at')->index();
            $t->integer('points_balance')->default(0);
            $t->timestamps(); $t->softDeletes();
        });
        Schema::create('student_supervisor_history', function (Blueprint $t) {
            $t->id(); $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('from_supervisor_id')->nullable()->constrained('supervisors')->nullOnDelete();
            $t->foreignId('to_supervisor_id')->nullable()->constrained('supervisors')->nullOnDelete();
            $t->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('reason')->nullable(); $t->timestamp('changed_at')->useCurrent();
            $t->index('student_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('student_supervisor_history'); Schema::dropIfExists('students'); Schema::dropIfExists('supervisors');
    }
};
