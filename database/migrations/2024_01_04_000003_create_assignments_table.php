<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('max_points')->default(100);
            $table->dateTime('due_date')->nullable();
            $table->text('starter_code')->nullable();
            $table->string('starter_language', 30)->default('python');
            $table->boolean('auto_workspace')->default(true);
            $table->timestamps();

            $table->index('course_id');
            $table->index('due_date');
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('workspace_snapshot_path')->nullable();
            $table->text('code_snapshot')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded', 'late'])->default('not_started');
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedSmallInteger('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
    }
};
