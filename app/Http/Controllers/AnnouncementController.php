<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(StoreAnnouncementRequest $request, Course $course)
    {
        $validated = $request->validated();

        $course->announcements()->create([
            'author_id' => Auth::id(),
            'title'     => $validated['title'],
            'body'      => $validated['body'],
            'pinned'    => $request->boolean('pinned'),
        ]);

        return back()->with('success', 'Announcement posted!');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}
