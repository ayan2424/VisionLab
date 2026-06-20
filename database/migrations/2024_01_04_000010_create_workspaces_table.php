<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('container_id')->unique()->nullable();
            $table->integer('port')->unique()->nullable();
            $table->enum('status', ['pending', 'running', 'stopped', 'error'])->default('pending');
            $table->enum('type', ['governed', 'independent'])->default('governed');
            $table->string('subscription_id')->nullable();
            $table->enum('governance_level', ['strict', 'moderate', 'none'])->default('strict');
            $table->string('language', 30)->default('python');
            $table->timestamps();

            $table->index('course_id');
            $table->index('student_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};