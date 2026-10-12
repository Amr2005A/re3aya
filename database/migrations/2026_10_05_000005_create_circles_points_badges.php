<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('circles', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->text('description')->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0); $t->boolean('is_active')->default(true);
            $t->timestamps(); $t->softDeletes();
        });
        Schema::create('circle_criteria', function (Blueprint $t) {
            $t->id(); $t->foreignId('circle_id')->constrained()->restrictOnDelete();
            $t->string('name'); $t->integer('default_points')->default(0);
            $t->unsignedSmallInteger('sort_order')->default(0); $t->boolean('is_active')->default(true);
            $t->timestamps(); $t->softDeletes();
        });
        Schema::create('badges', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->text('how_to_earn')->nullable();
            $t->unsignedInteger('points')->default(0); $t->text('description')->nullable();
            $t->string('icon_path')->nullable(); $t->boolean('is_repeatable')->default(false);
            $t->boolean('is_active')->default(true); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('badge_awards', function (Blueprint $t) {
            $t->id(); $t->foreignId('student_id')->constrained()->restrictOnDelete();
            $t->foreignId('badge_id')->constrained()->restrictOnDelete();
            $t->integer('points_awarded')->default(0); // لقطة
            $t->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('awarded_at')->useCurrent(); $t->string('note')->nullable();
            $t->uuid('batch_id')->nullable()->index(); $t->timestamps();
            $t->index(['student_id','badge_id']);
        });
        Schema::create('point_transactions', function (Blueprint $t) {
            $t->id(); $t->foreignId('student_id')->constrained()->restrictOnDelete();
            $t->integer('points'); $t->string('type', 20)->index(); // award|badge|reversal
            $t->string('reason');
            $t->foreignId('circle_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('criteria_id')->nullable()->constrained('circle_criteria')->nullOnDelete();
            $t->foreignId('badge_award_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('reverses_id')->nullable()->unique()->constrained('point_transactions')->nullOnDelete();
            $t->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('occurred_at')->index(); $t->string('note')->nullable(); $t->timestamps();
            $t->index(['student_id','occurred_at']);
        });
    }
    public function down(): void {
        foreach (['point_transactions','badge_awards','badges','circle_criteria','circles'] as $x) Schema::dropIfExists($x);
    }
};
