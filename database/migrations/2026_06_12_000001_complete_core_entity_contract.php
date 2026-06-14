<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CORE_ENTITY_CONTRACT — Complete Schema Migration
 *
 * This migration completes the VisionLab data model defined in PROMPTS.xml
 * CORE_ENTITY_CONTRACT. It adds missing tables and augments existing ones
 * with columns required by all 12 implementation phases.
 *
 * New tables: workspace_collaborators, collab_sessions, push_subscriptions,
 *   workspace_quotas, user_badges, submission_forensics, deployments,
 *   audit_logs, notification_preferences, announcement_reads
 *
 * Augmented tables: users, workspaces, extensions, ai_chat_sessions,
 *   ai_messages, ai_actions_log, ai_pending_patches, ai_snapshots,
 *   analytics_events
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Augment: users ──────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('users', 'current_streak')) {
                $table->unsignedInteger('current_streak')->default(0)->after('last_activity_at');
            }
            if (! Schema::hasColumn('users', 'longest_streak')) {
                $table->unsignedInteger('longest_streak')->default(0)->after('current_streak');
            }
        });

        // ── Augment: workspaces ─────────────────────────────────────────
        Schema::table('workspaces', function (Blueprint $table) {
            if (! Schema::hasColumn('workspaces', 'token')) {
                $table->string('token', 64)->nullable()->after('port');
            }
            if (! Schema::hasColumn('workspaces', 'storage_path')) {
                $table->string('storage_path')->nullable()->after('token');
            }
            if (! Schema::hasColumn('workspaces', 'heartbeat_at')) {
                $table->timestamp('heartbeat_at')->nullable()->after('storage_path');
            }
            if (! Schema::hasColumn('workspaces', 'quota_data')) {
                $table->json('quota_data')->nullable()->after('heartbeat_at');
            }
            if (! Schema::hasColumn('workspaces', 'proxy_url')) {
                $table->string('proxy_url')->nullable()->after('quota_data');
            }
            if (! Schema::hasColumn('workspaces', 'container_image')) {
                $table->string('container_image')->nullable()->after('proxy_url');
            }
        });

        // ── Augment: extensions ─────────────────────────────────────────
        Schema::table('extensions', function (Blueprint $table) {
            if (! Schema::hasColumn('extensions', 'category')) {
                $table->string('category', 40)->default('utility')->after('description');
            }
            if (! Schema::hasColumn('extensions', 'artifact_path')) {
                $table->string('artifact_path')->nullable()->after('category');
            }
            if (! Schema::hasColumn('extensions', 'checksum')) {
                $table->string('checksum', 128)->nullable()->after('artifact_path');
            }
            if (! Schema::hasColumn('extensions', 'source')) {
                $table->string('source')->nullable()->after('checksum');
            }
            if (! Schema::hasColumn('extensions', 'is_required')) {
                $table->boolean('is_required')->default(false)->after('is_builtin');
            }
            if (! Schema::hasColumn('extensions', 'rollout_state')) {
                $table->enum('rollout_state', ['draft', 'staged', 'released', 'deprecated'])->default('released')->after('is_active');
            }
        });

        // ── Augment: ai_chat_sessions ───────────────────────────────────
        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_chat_sessions', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('user_id');
                $table->foreign('workspace_id')->references('id')->on('workspaces')->nullOnDelete();
            }
            if (! Schema::hasColumn('ai_chat_sessions', 'token_total')) {
                $table->unsignedInteger('token_total')->default(0)->after('mode');
            }
            if (! Schema::hasColumn('ai_chat_sessions', 'context_metadata')) {
                $table->json('context_metadata')->nullable()->after('token_total');
            }
            if (! Schema::hasColumn('ai_chat_sessions', 'provider_metadata')) {
                $table->json('provider_metadata')->nullable()->after('context_metadata');
            }
        });

        // ── Augment: ai_messages ────────────────────────────────────────
        Schema::table('ai_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_messages', 'tool_name')) {
                $table->string('tool_name', 80)->nullable()->after('content');
            }
            if (! Schema::hasColumn('ai_messages', 'tool_input')) {
                $table->json('tool_input')->nullable()->after('tool_name');
            }
            if (! Schema::hasColumn('ai_messages', 'tool_output')) {
                $table->json('tool_output')->nullable()->after('tool_input');
            }
            if (! Schema::hasColumn('ai_messages', 'safety_flags')) {
                $table->json('safety_flags')->nullable()->after('token_count');
            }
        });

        // ── Augment: ai_actions_log ─────────────────────────────────────
        Schema::table('ai_actions_log', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_actions_log', 'workspace_id')) {
                $table->unsignedBigInteger('workspace_id')->nullable()->after('user_id');
                $table->foreign('workspace_id')->references('id')->on('workspaces')->nullOnDelete();
            }
            if (! Schema::hasColumn('ai_actions_log', 'session_id')) {
                $table->unsignedBigInteger('session_id')->nullable()->after('workspace_id');
                $table->foreign('session_id')->references('id')->on('ai_chat_sessions')->nullOnDelete();
            }
            if (! Schema::hasColumn('ai_actions_log', 'content_hashes')) {
                $table->json('content_hashes')->nullable()->after('diff_summary');
            }
            if (! Schema::hasColumn('ai_actions_log', 'trigger_source')) {
                $table->string('trigger_source', 40)->nullable()->after('content_hashes');
            }
            if (! Schema::hasColumn('ai_actions_log', 'result')) {
                $table->string('result', 40)->nullable()->after('trigger_source');
            }
        });

        // ── Augment: ai_pending_patches ─────────────────────────────────
        Schema::table('ai_pending_patches', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_pending_patches', 'original_hash')) {
                $table->string('original_hash', 128)->nullable()->after('diff');
            }
            if (! Schema::hasColumn('ai_pending_patches', 'patched_hash')) {
                $table->string('patched_hash', 128)->nullable()->after('original_hash');
            }
            if (! Schema::hasColumn('ai_pending_patches', 'reviewer_id')) {
                $table->unsignedBigInteger('reviewer_id')->nullable()->after('created_by');
                $table->foreign('reviewer_id')->references('id')->on('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('ai_pending_patches', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewer_id');
            }
            if (! Schema::hasColumn('ai_pending_patches', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('reviewed_at');
            }
        });

        // ── Augment: ai_snapshots ───────────────────────────────────────
        Schema::table('ai_snapshots', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_snapshots', 'content_hash')) {
                $table->string('content_hash', 128)->nullable()->after('content');
            }
            if (! Schema::hasColumn('ai_snapshots', 'session_id')) {
                $table->unsignedBigInteger('session_id')->nullable()->after('workspace_id');
                $table->foreign('session_id')->references('id')->on('ai_chat_sessions')->nullOnDelete();
            }
        });

        // ── Augment: analytics_events ───────────────────────────────────
        Schema::table('analytics_events', function (Blueprint $table) {
            if (! Schema::hasColumn('analytics_events', 'resource_type')) {
                $table->string('resource_type', 60)->nullable()->after('event_data');
            }
            if (! Schema::hasColumn('analytics_events', 'resource_id')) {
                $table->unsignedBigInteger('resource_id')->nullable()->after('resource_type');
            }
            if (! Schema::hasColumn('analytics_events', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('resource_id');
            }
            if (! Schema::hasColumn('analytics_events', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (! Schema::hasColumn('analytics_events', 'correlation_id')) {
                $table->uuid('correlation_id')->nullable()->after('user_agent');
            }
        });

        // ══════════════════════════════════════════════════════════════════
        // NEW TABLES
        // ══════════════════════════════════════════════════════════════════

        // ── announcement_reads ──────────────────────────────────────────
        if (! Schema::hasTable('announcement_reads')) {
            Schema::create('announcement_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('read_at')->useCurrent();

                $table->unique(['announcement_id', 'user_id']);
                $table->index('user_id');
            });
        }

        // ── workspace_collaborators ─────────────────────────────────────
        if (! Schema::hasTable('workspace_collaborators')) {
            Schema::create('workspace_collaborators', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->enum('role', ['owner', 'collaborator', 'viewer'])->default('collaborator');
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamps();

                $table->unique(['workspace_id', 'user_id']);
                $table->index('user_id');
            });
        }

        // ── collab_sessions ─────────────────────────────────────────────
        if (! Schema::hasTable('collab_sessions')) {
            Schema::create('collab_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->json('cursor_state')->nullable();
                $table->json('selection_state')->nullable();
                $table->boolean('is_online')->default(false);
                $table->timestamp('heartbeat_at')->nullable();
                $table->string('color', 10)->nullable();
                $table->timestamps();

                $table->index(['workspace_id', 'is_online']);
                $table->index('user_id');
            });
        }

        // ── push_subscriptions ──────────────────────────────────────────
        if (! Schema::hasTable('push_subscriptions')) {
            Schema::create('push_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('endpoint');
                $table->string('p256dh_key');
                $table->string('auth_token');
                $table->string('content_encoding', 20)->default('aesgcm');
                $table->json('browser_info')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
            });
        }

        // ── workspace_quotas ────────────────────────────────────────────
        if (! Schema::hasTable('workspace_quotas')) {
            Schema::create('workspace_quotas', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedInteger('memory_mb')->default(512);
                $table->unsignedInteger('cpu_shares')->default(1024);
                $table->unsignedInteger('disk_mb')->default(1024);
                $table->unsignedInteger('timeout_minutes')->default(120);
                $table->enum('scope', ['global', 'course', 'user'])->default('global');
                $table->unsignedBigInteger('scope_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['scope', 'scope_id']);
                $table->index('is_active');
            });
        }

        // ── user_badges ─────────────────────────────────────────────────
        if (! Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('badge_type', 60);
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('icon', 60)->nullable();
                $table->timestamp('earned_at')->useCurrent();
                $table->string('source_event')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'badge_type']);
                $table->index('badge_type');
            });
        }

        // ── submission_forensics ────────────────────────────────────────
        if (! Schema::hasTable('submission_forensics')) {
            Schema::create('submission_forensics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('workspace_id')->nullable();
                $table->unsignedInteger('human_keystrokes')->default(0);
                $table->unsignedInteger('ai_patches_applied')->default(0);
                $table->unsignedInteger('pasted_count')->default(0);
                $table->unsignedInteger('imported_count')->default(0);
                $table->decimal('human_pct', 5, 2)->default(100.00);
                $table->decimal('ai_pct', 5, 2)->default(0.00);
                $table->enum('confidence', ['high', 'medium', 'low'])->default('low');
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();

                $table->foreign('workspace_id')->references('id')->on('workspaces')->nullOnDelete();
                $table->unique('submission_id');
            });
        }

        // ── deployments ─────────────────────────────────────────────────
        if (! Schema::hasTable('deployments')) {
            Schema::create('deployments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('provider', 40)->default('vercel');
                $table->string('deployment_id')->nullable();
                $table->string('public_url')->nullable();
                $table->enum('status', ['queued', 'building', 'deployed', 'failed', 'cancelled'])->default('queued');
                $table->json('job_metadata')->nullable();
                $table->text('error_summary')->nullable();
                $table->timestamp('deployed_at')->nullable();
                $table->boolean('notification_sent')->default(false);
                $table->timestamps();

                $table->index(['workspace_id', 'status']);
                $table->index('user_id');
            });
        }

        // ── audit_logs ──────────────────────────────────────────────────
        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('action', 80);
                $table->string('resource_type', 60);
                $table->unsignedBigInteger('resource_id')->nullable();
                $table->json('old_state')->nullable();
                $table->json('new_state')->nullable();
                $table->string('result', 40)->default('success');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->uuid('correlation_id')->nullable();
                $table->timestamps();

                $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
                $table->index(['resource_type', 'resource_id']);
                $table->index('actor_id');
                $table->index('action');
                $table->index('created_at');
                $table->index('correlation_id');
            });
        }

        // ── notification_preferences ────────────────────────────────────
        if (! Schema::hasTable('notification_preferences')) {
            Schema::create('notification_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->json('channel_prefs')->nullable();
                $table->json('event_prefs')->nullable();
                $table->time('quiet_hours_start')->nullable();
                $table->time('quiet_hours_end')->nullable();
                $table->timestamps();

                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('deployments');
        Schema::dropIfExists('submission_forensics');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('workspace_quotas');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('collab_sessions');
        Schema::dropIfExists('workspace_collaborators');
        Schema::dropIfExists('announcement_reads');

        // Reverse column additions would go here in production,
        // but for development we rely on migrate:fresh
    }
};
