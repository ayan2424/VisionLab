<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // enrollments table is already created by 2024_01_04_000002_create_courses_table
        // This migration is a no-op kept for migration history integrity
        if (! Schema::hasColumn('enrollments', 'enrolled_at')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->timestamp('enrolled_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void {}
};
