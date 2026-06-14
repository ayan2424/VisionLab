<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * AuditService — Centralized audit logging for all sensitive operations.
 *
 * Wraps the AuditLog::record() static method with convenient
 * domain-specific helpers for common security-relevant actions.
 */
class AuditService
{
    /**
     * Log an auditable action.
     *
     * @param string      $action       The action performed (e.g., 'user.suspend', 'course.create')
     * @param string      $resourceType The type of entity affected (e.g., 'User', 'Course')
     * @param int|null    $resourceId   The ID of the affected entity
     * @param array|null  $oldState     Previous state snapshot
     * @param array|null  $newState     New state snapshot
     * @param string      $result       Outcome: 'success', 'failure', 'denied'
     */
    public static function log(
        string $action,
        string $resourceType = '',
        ?int $resourceId = null,
        ?array $oldState = null,
        ?array $newState = null,
        string $result = 'success',
    ): AuditLog {
        return AuditLog::record(
            action: $action,
            resourceType: $resourceType,
            resourceId: $resourceId,
            oldState: $oldState,
            newState: $newState,
            result: $result,
        );
    }

    /**
     * Convenience methods for common audit actions.
     */
    public static function userSuspended(int $userId, int $byAdmin): AuditLog
    {
        return self::log('user.suspended', 'User', $userId,
            oldState: ['status' => 'active'],
            newState: ['status' => 'suspended', 'suspended_by' => $byAdmin],
        );
    }

    public static function userActivated(int $userId, int $byAdmin): AuditLog
    {
        return self::log('user.activated', 'User', $userId,
            oldState: ['status' => 'suspended'],
            newState: ['status' => 'active', 'activated_by' => $byAdmin],
        );
    }

    public static function courseCreated(int $courseId): AuditLog
    {
        return self::log('course.created', 'Course', $courseId);
    }

    public static function patchApproved(int $patchId): AuditLog
    {
        return self::log('ai.patch_approved', 'AiPendingPatch', $patchId);
    }

    public static function patchRejected(int $patchId): AuditLog
    {
        return self::log('ai.patch_rejected', 'AiPendingPatch', $patchId);
    }

    public static function workspaceStarted(int $workspaceId): AuditLog
    {
        return self::log('workspace.started', 'Workspace', $workspaceId);
    }

    public static function deploymentTriggered(int $deploymentId, string $provider): AuditLog
    {
        return self::log('deployment.triggered', 'Deployment', $deploymentId,
            newState: ['provider' => $provider],
        );
    }
}
