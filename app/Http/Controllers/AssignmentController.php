<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function create(Course $course)
    {
        $this->authorize('update', $course);
        return view('assignments.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title'            => 'required|string|max:150',
            'description'      => 'nullable|string|max:5000',
            'max_points'       => 'required|integer|min:1|max:1000',
            'due_date'         => 'nullable|date|after:now',
            'starter_code'     => 'nullable|string|max:20000',
            'starter_language' => 'required|string|in:python,javascript,typescript,php,java,c,cpp,rust,go,ruby,bash',
            'auto_workspace'   => 'sometimes|boolean',
        ]);

        $assignment = $course->assignments()->create($validated);

        return redirect()->route('courses.show', [$course->slug, 'tab' => 'assignments'])
                         ->with('success', 'Assignment created successfully!');
    }

    public function show(Assignment $assignment)
    {
        $user   = Auth::user();
        $course = $assignment->course()->with('instructor')->first();

        $isInstructor = $user->id === $course->instructor_id || $user->isAdmin();
        $submission   = $assignment->submissionFor($user);

        if ($isInstructor) {
            $submissions = $assignment->submissions()->with('student')->get();
        } else {
            $submissions = collect();
        }

        return view('assignments.show', compact(
            'assignment', 'course', 'isInstructor', 'submission', 'submissions'
        ));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment->course);
        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment->course);

        $validated = $request->validate([
            'title'            => 'required|string|max:150',
            'description'      => 'nullable|string|max:5000',
            'max_points'       => 'required|integer|min:1|max:1000',
            'due_date'         => 'nullable|date',
            'starter_code'     => 'nullable|string|max:20000',
            'starter_language' => 'required|string',
        ]);

        $assignment->update($validated);

        return redirect()->route('assignments.show', $assignment)
                         ->with('success', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('update', $assignment->course);
        $assignment->delete();

        return redirect()->route('courses.show', [$assignment->course->slug, 'tab' => 'assignments'])
                         ->with('success', 'Assignment deleted.');
    }
}
