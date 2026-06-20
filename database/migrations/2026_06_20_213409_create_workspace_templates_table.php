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
        Schema::create('workspace_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Laravel 11', 'Next.js'
            $table->string('description')->nullable();
            $table->string('git_url')->nullable(); // Base repository to clone
            $table->string('language')->default('python');
            $table->text('start_command')->nullable(); // e.g., 'php artisan serve'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Add template_id to workspaces
        Schema::table('workspaces', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->constrained('workspace_templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });
        Schema::dropIfExists('workspace_templates');
    }
};
