<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Private Channel — Per-User Notifications (Phase 6)
|--------------------------------------------------------------------------
| Used for real-time grading alerts sent to the student whose submission
| was just graded via the SubmissionGraded broadcast event.
*/
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Presence Channel — Collaborative Workspace collab.{roomSlug}
|--------------------------------------------------------------------------
| Returns the user payload for Echo here / joining / leaving events.
| workspace.blade.php joins this channel by default.
*/
Broadcast::channel('collab.{roomSlug}', function ($user, $roomSlug) {
    // Determine the ID based on the prefix (e.g. ws-5 or course-10)
    $id = null;
    if (str_starts_with($roomSlug, 'ws-')) {
        $id = str_replace('ws-', '', $roomSlug);
        $workspace = \App\Models\Workspace::find($id);
        if (!$workspace) return false;
        
        // Authorization: Must be owner, collaborator, or instructor of the course
        $canAccess = $workspace->user_id === $user->id || 
                     $workspace->collaborators()->where('user_id', $user->id)->exists() ||
                     ($workspace->course && $workspace->course->instructor_id === $user->id);
                     
        if (!$canAccess) return false;
    }

    $colors = ['#7c3aed', '#2563eb', '#0891b2', '#16a34a', '#dc2626', '#d97706', '#db2777'];

    return [
        'id'       => $user->id,
        'name'     => $user->name,
        'initials' => $user->avatar_initials,
        'color'    => $colors[$user->id % count($colors)],
        'role'     => $user->role,
    ];
});

/*
|--------------------------------------------------------------------------
| Presence Channel — Workspace (legacy alias kept for compatibility)
|--------------------------------------------------------------------------
*/
Broadcast::channel('workspace.{roomId}', function ($user, $roomId) {
    $colors = ['#7c3aed', '#2563eb', '#0891b2', '#16a34a', '#dc2626', '#d97706', '#db2777'];

    return [
        'id'       => $user->id,
        'name'     => $user->name,
        'initials' => $user->avatar_initials,
        'color'    => $colors[$user->id % count($colors)],
        'role'     => $user->role,
    ];
});
