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
        DB::table('system_configs')->insert([
            'key' => 'global_allow_marketplace',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Whether VS Code extensions marketplace is allowed globally.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_configs')->where('key', 'global_allow_marketplace')->delete();
    }
};
