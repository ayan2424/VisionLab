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
            $table->longText('nix_config')->nullable()->after('start_command');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspace_templates', function (Blueprint $table) {
            $table->dropColumn('nix_config');
        });
    }
};
