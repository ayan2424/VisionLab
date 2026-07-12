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
        Schema::dropIfExists('video_attendances');
        Schema::dropIfExists('video_rooms');
        Schema::dropIfExists('recording_audit_logs');
        Schema::dropIfExists('recordings');
        Schema::dropIfExists('deployments');
        Schema::dropIfExists('collab_sessions');
        Schema::dropIfExists('workspace_collaborators');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('rooms');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down method implemented since these features are being permanently removed.
    }
};
