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
        Schema::table('workspace_templates', function (Blueprint $table) {
            $table->text('bootstrap_script')->nullable()->after('nix_config');
            $table->json('ui_parameters')->nullable()->after('bootstrap_script');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspace_templates', function (Blueprint $table) {
            $table->dropColumn(['bootstrap_script', 'ui_parameters']);
        });
    }
};
