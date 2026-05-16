<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $course = Course::create([
            'instructor_id' => Auth::id(),
            'title'         => $validated['title'],
            'description'   => $validated['description'] ?? null,
            'cover_image'   => $coverPath,
        ]);

        return redirect()->route('courses.show', $course->slug)
                         ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'announcements.author', 'assignments']);

        $user = Auth::user();
        $isInstructor = $user->id === $course->instructor_id || $user->isAdmin();
        $isEnrolled   = $course->isEnrolled($user);

        if (!$isInstructor && !$isEnrolled) {
            abort(403, 'You are not enrolled in this course.');
        }

        $students  = $course->students()->get();
        $userSubmissions = [];
        if ($user->isStudent()) {
            foreach ($course->assignments as $assignment) {
                $userSubmissions[$assignment->id] = $assignment->submissionFor($user);
            }
        }

        $tab = request('tab', 'stream');

        return view('courses.show', compact(
            'course', 'isInstructor', 'isEnrolled', 'students',
            'userSubmissions', 'tab'
        ));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|max:2048',
            'is_active'   => 'sometimes|boolean',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) Storage::disk('public')->delete($course->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $course->update($validated);

        return redirect()->route('courses.show', $course->slug)
                         ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        if ($course->cover_image) Storage::disk('public')->delete($course->cover_image);
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted.');
    }
}
