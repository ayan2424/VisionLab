<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. VisionGuard AI Forensics
        Schema::create('submission_forensics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('human_keystrokes')->default(0);
            $table->unsignedBigInteger('ai_injected_chars')->default(0);
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->timestamps();
        });

        // 2. Docker Resource Quotas
        Schema::create('workspace_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('memory_limit', 20)->default('512m');
            $table->string('cpu_limit', 10)->default('0.5');
            $table->timestamps();

            // Should be either user-specific or course-specific
            $table->unique(['user_id', 'course_id']);
        });

        // 3. One-Click Cloud Deployments
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('provider', 50)->default('vercel');
            $table->string('project_name');
            $table->string('url')->nullable();
            $table->string('status', 20)->default('pending'); // pending, success, failed
            $table->text('error_log')->nullable();
            $table->timestamps();
        });

        // 4. Gamification: User Badges
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_name');
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['user_id', 'badge_name']);
        });

        // 5. Gamification: Coding Sessions (for Heatmap)
        Schema::create('coding_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('commits_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coding_sessions');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('deployments');
        Schema::dropIfExists('workspace_quotas');
        Schema::dropIfExists('submission_forensics');
    }
};
