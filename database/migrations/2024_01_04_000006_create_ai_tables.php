<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('workspace_ref')->nullable();
            $table->string('title')->default('New Chat');
            $table->enum('mode', ['CHAT', 'PLAN', 'AGENT'])->default('CHAT');
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ai_chat_sessions')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant'])->default('user');
            $table->text('content');
            $table->unsignedInteger('token_count')->default(0);
            $table->timestamps();

            $table->index('session_id');
        });

        Schema::create('ai_actions_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('workspace_ref')->nullable();
            $table->string('action_type', 50);
            $table->string('file_path')->nullable();
            $table->text('diff_summary')->nullable();
            $table->enum('mode', ['CHAT', 'PLAN', 'AGENT'])->default('CHAT');
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('collab_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('workspace_ref');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index('workspace_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collab_chat_messages');
        Schema::dropIfExists('ai_actions_log');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_chat_sessions');
    }
};
