<?php

namespace App\Http\Controllers;

use App\Events\NewAnnouncement;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        if ($user->id !== $course->instructor_id && ! $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string|max:5000',
            'pinned' => 'sometimes|boolean',
        ]);

        $announcement = $course->announcements()->create([
            'author_id' => $user->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'pinned' => $request->boolean('pinned'),
        ]);

        // Broadcast to enrolled students
        try {
            broadcast(new NewAnnouncement(
                $course->id,
                $course->title,
                $validated['title'],
                $user->name,
                Str::limit(strip_tags($validated['body']), 100),
            ))->toOthers();
        } catch (\Throwable $e) {
            // Reverb may not be running
        }

        return back()->with('success', 'Announcement posted!');
    }

    public function destroy(Announcement $announcement)
    {
        $user = Auth::user();
        $course = $announcement->course;

        if ($user->id !== $course->instructor_id && $user->id !== $announcement->author_id && ! $user->isAdmin()) {
            abort(403);
        }

        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}
