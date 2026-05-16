<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('category', 50)->default('general');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('category');
        });

        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('model_id');
            $table->string('display_name');
            $table->enum('role', ['chat', 'autocomplete', 'agent', 'edit'])->default('chat');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('context_length')->default(128000);
            $table->json('config')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['role', 'is_active']);
            $table->index('provider');
            $table->unique(['provider', 'model_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
        Schema::dropIfExists('ai_settings');
    }
};
