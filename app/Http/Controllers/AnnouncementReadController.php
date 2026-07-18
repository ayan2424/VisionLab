<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementReadController extends Controller
{
    public function markAsRead(Request $request, Announcement $announcement)
    {
        $user = Auth::user();

        // Ensure the student is enrolled in the course that has this announcement
        $course = $announcement->course;
        if ($course && !$user->isAdmin() && $user->id !== $course->instructor_id && !$course->isEnrolled($user)) {
            abort(403, 'Unauthorized access.');
        }

        AnnouncementRead::firstOrCreate([
            'announcement_id' => $announcement->id,
            'user_id' => $user->id,
        ], [
            'read_at' => now(),
        ]);

        return back()->with('success', 'Announcement marked as read.');
    }
}
