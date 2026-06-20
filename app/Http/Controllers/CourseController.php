<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $courses = Course::with('instructor')->withCount('students')->latest()->paginate(12);
        } elseif ($user->isInstructor()) {
            $courses = Course::where('instructor_id', $user->id)->withCount('students')->latest()->paginate(12);
        } else {
            $courses = Course::whereHas('enrollments', function ($q) use ($user) {
                $q->where('student_id', $user->id)->where('status', 'active');
            })->with('instructor')->withCount('students')->latest()->paginate(12);
        }

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        return view('courses.create');
    }

    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $course = Course::create([
            'instructor_id' => Auth::id(),
            'title'         => $validated['title'],
            'description'   => $validated['description'] ?? null,
            'is_active'     => $validated['is_active'] ?? true,
            'cover_image'   => $coverPath,
        ]);

        return redirect()->route('courses.show', $course->slug)
                         ->with('success', 'Course created successfully!');
    }

    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)
                        ->with(['instructor', 'announcements.author', 'assignments'])
                        ->firstOrFail();

        $user = Auth::user();
        $this->authorize('view', $course);

        $isInstructor = $user->id === $course->instructor_id || $user->isAdmin();
        $isEnrolled   = $course->isEnrolled($user);

        $students  = $course->students()->get();
        $userSubmissions = [];
        $readAnnouncementIds = [];

        if ($user->isStudent()) {
            $submissions = \App\Models\Submission::where('student_id', $user->id)
                ->whereIn('assignment_id', $course->assignments->pluck('id'))
                ->get()
                ->keyBy('assignment_id');

            foreach ($course->assignments as $assignment) {
                $userSubmissions[$assignment->id] = $submissions->get($assignment->id);
            }
            $readAnnouncementIds = \App\Models\AnnouncementRead::where('user_id', $user->id)
                ->whereIn('announcement_id', $course->announcements->pluck('id'))
                ->pluck('announcement_id')
                ->toArray();
        }

        $tab = request('tab', 'stream');

        return view('courses.show', compact(
            'course', 'isInstructor', 'isEnrolled', 'students',
            'userSubmissions', 'readAnnouncementIds', 'tab'
        ));
    }

    public function edit(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $this->authorize('update', $course);
        return view('courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request, string $slug)
    {
        $course    = Course::where('slug', $slug)->firstOrFail();
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) Storage::disk('public')->delete($course->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $course->update($validated);

        return redirect()->route('courses.show', $course->slug)
                         ->with('success', 'Course updated successfully!');
    }

    public function destroy(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $this->authorize('delete', $course);

        if ($course->cover_image) Storage::disk('public')->delete($course->cover_image);
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted.');
    }
}
