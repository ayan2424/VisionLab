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

/*
|--------------------------------------------------------------------------
| Presence Channel — Course (for real-time announcements)
|--------------------------------------------------------------------------
| Used to push NewAnnouncement events to enrolled students.
*/
Broadcast::channel('course.{courseId}', function ($user, $courseId) {
    $course = \App\Models\Course::find($courseId);
    if (!$course) return false;

    // Allow instructor, admin, or enrolled students
    if ($user->isAdmin() || $user->id === $course->instructor_id || $course->isEnrolled($user)) {
        return [
            'id'   => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    return false;
});
