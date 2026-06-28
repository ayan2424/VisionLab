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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. bedrock, anthropic, openai
            $table->string('title')->nullable(); // e.g. AWS Bedrock
            $table->string('api_key')->nullable();
            $table->string('api_base')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->string('title'); // e.g. Claude 3.5 Sonnet
            $table->string('model'); // e.g. anthropic.claude-3-5-sonnet-20240620-v1:0
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
        Schema::dropIfExists('ai_providers');
    }
};
