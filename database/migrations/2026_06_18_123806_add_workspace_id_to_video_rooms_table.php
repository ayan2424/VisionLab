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
        Schema::table('video_rooms', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->constrained('workspaces')->cascadeOnDelete();
            // A video room can belong to either a course or a workspace, so course_id must be nullable now.
            $table->unsignedBigInteger('course_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_rooms', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropColumn('workspace_id');
            $table->unsignedBigInteger('course_id')->nullable(false)->change();
        });
    }
};
