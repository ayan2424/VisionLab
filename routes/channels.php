<?php

use App\Models\Workspace;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Private Channel — Per-User Notifications
|--------------------------------------------------------------------------
| Used for real-time grading alerts sent to the student whose submission
| was just graded via the SubmissionGraded broadcast event.
*/
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Private Channel — Workspace Collaboration / Live Events
|--------------------------------------------------------------------------
| Authorizes only workspace owners, assigned course instructors, and admins.
*/
Broadcast::channel('collab.ws-{workspaceId}', function ($user, $workspaceId) {
    if (!ctype_digit((string) $workspaceId)) {
        return false;
    }

    $workspace = Workspace::find((int) $workspaceId);

    return $workspace !== null && $user->can('access', $workspace);
});

/*
|--------------------------------------------------------------------------
| Presence Channel — Active Workspace Collaboration
|--------------------------------------------------------------------------
| Authorizes only users permitted to access the target workspace.
*/
Broadcast::channel('workspace.{id}', function ($user, $id) {
    $workspace = ctype_digit((string) $id)
        ? Workspace::find((int) $id)
        : Workspace::where('slug', (string) $id)->first();

    if ($workspace === null || !$user->can('access', $workspace)) {
        return false;
    }

    return [
        'id'   => $user->id,
        'name' => $user->name,
        'role' => $user->role,
    ];
});

/*
|--------------------------------------------------------------------------
| Private Channel — Workspace AI Patch Stream
|--------------------------------------------------------------------------
| Authorizes only users permitted to view the workspace identified by slug.
*/
Broadcast::channel('workspace.{slug}.patches', function ($user, string $slug) {
    $workspace = Workspace::where('slug', $slug)->first();

    return $workspace !== null && $user->can('view', $workspace);
});
