<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Workspace-extension pivot table for per-workspace extension toggling
        if (!Schema::hasTable('workspace_extensions')) {
            Schema::create('workspace_extensions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->foreignId('extension_id')->constrained('extensions')->cascadeOnDelete();
                $table->boolean('is_enabled')->default(true);
                $table->timestamps();

                $table->unique(['room_id', 'extension_id']);
            });
        }

        // Collab sessions for tracking active collaboration
        if (!Schema::hasTable('collab_sessions')) {
            Schema::create('collab_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('cursor_file')->nullable();
                $table->unsignedInteger('cursor_line')->nullable();
                $table->unsignedInteger('cursor_col')->nullable();
                $table->string('cursor_color', 7)->default('#7c3aed');
                $table->timestamp('last_heartbeat')->nullable();
                $table->timestamps();

                $table->unique(['room_id', 'user_id']);
                $table->index('room_id');
            });
        }

        // Video rooms for persisting video call data
        if (!Schema::hasTable('video_rooms')) {
            Schema::create('video_rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->string('jitsi_room_name');
                $table->string('jitsi_domain')->default('meet.jit.si');
                $table->foreignId('started_by')->constrained('users')->cascadeOnDelete();
                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('ended_at')->nullable();
                $table->enum('status', ['active', 'ended'])->default('active');
                $table->timestamps();

                $table->index(['room_id', 'status']);
            });
        }

        // Push subscriptions for web push notifications
        if (!Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('endpoint');
                $table->string('public_key')->nullable();
                $table->string('auth_token')->nullable();
                $table->string('content_encoding')->nullable();
                $table->timestamps();

                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('video_rooms');
        Schema::dropIfExists('collab_sessions');
        Schema::dropIfExists('workspace_extensions');
    }
};
