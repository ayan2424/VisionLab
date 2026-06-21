<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalAnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $announcements = Announcement::whereNull('course_id')
            ->orderBy('pinned', 'desc')
            ->latest()
            ->paginate(15);
            
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string|max:5000',
            'pinned' => 'sometimes|boolean',
        ]);

        Announcement::create([
            'course_id' => null,
            'author_id' => Auth::id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
            'pinned' => $request->boolean('pinned'),
        ]);

        return redirect()->route('announcements.index')->with('success', 'Global Announcement posted!');
    }

    public function destroy(Announcement $announcement)
    {
        if (!Auth::user()->isAdmin() || $announcement->course_id !== null) {
            abort(403);
        }

        $announcement->delete();

        return back()->with('success', 'Global Announcement deleted.');
    }
}
