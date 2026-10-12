<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('working_days', function (Blueprint $t) {
            $t->id(); $t->unsignedTinyInteger('weekday')->unique(); // 0=الأحد ... 6=السبت
            $t->boolean('is_active')->default(true); $t->timestamps();
        });
        Schema::create('attendance_records', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->restrictOnDelete();
            $t->date('date')->index();
            $t->string('status', 20)->index();
            $t->foreignId('supervisor_id')->nullable()->constrained('supervisors')->nullOnDelete(); // لقطة وقت التسجيل
            $t->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('recorded_at')->useCurrent();
            $t->string('note')->nullable(); $t->timestamps();
            $t->unique(['student_id','date']);
        });
        Schema::create('notes', function (Blueprint $t) {
            $t->id(); $t->foreignId('student_id')->constrained()->restrictOnDelete();
            $t->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $t->text('body'); $t->timestamps(); $t->softDeletes();
            $t->index(['student_id','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('notes'); Schema::dropIfExists('attendance_records'); Schema::dropIfExists('working_days');
    }
};
