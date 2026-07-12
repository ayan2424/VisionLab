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

