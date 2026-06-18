<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum to string so we can support 'role' and any other custom scopes later
        Schema::table('workspace_quotas', function (Blueprint $table) {
            $table->string('scope', 50)->default('global')->change();
            // We also need scope_id to be a string or add scope_value
            $table->string('scope_value', 100)->nullable()->after('scope_id');
        });

        // Copy existing scope_id to scope_value
        DB::statement('UPDATE workspace_quotas SET scope_value = scope_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspace_quotas', function (Blueprint $table) {
            $table->dropColumn('scope_value');
            // Reverting scope change safely is tricky, so we'll just leave it as string.
        });
    }
};
