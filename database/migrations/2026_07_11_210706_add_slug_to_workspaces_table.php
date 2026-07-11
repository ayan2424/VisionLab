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
        // Add as nullable first to prevent MySQL constraint errors
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Backfill existing workspaces
        foreach (\App\Models\Workspace::all() as $w) {
            $slug = \Illuminate\Support\Str::slug($w->name ?: 'workspace-' . $w->id);
            $originalSlug = $slug;
            $count = 1;
            while (\App\Models\Workspace::where('slug', $slug)->where('id', '!=', $w->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $w->slug = $slug;
            $w->save();
        }

        // Now enforce unique and not null
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
