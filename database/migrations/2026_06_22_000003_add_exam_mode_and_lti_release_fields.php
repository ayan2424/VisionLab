<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('mode')->default('learning'); // learning or exam
            $table->boolean('allow_ai')->default(true);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('grade_published')->default(false);
            $table->decimal('draft_grade', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['mode', 'allow_ai']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['grade_published', 'draft_grade']);
        });
    }
};
