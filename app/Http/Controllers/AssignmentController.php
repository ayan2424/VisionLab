<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function create(Course $course)
    {
        $this->authorize('update', $course);
        return view('assignments.create', compact('course'));
    }

    public function store(StoreAssignmentRequest $request, Course $course)
    {
        $assignment = $course->assignments()->create($request->validated());

        return redirect()->route('courses.show', [$course->slug, 'tab' => 'assignments'])
                         ->with('success', 'Assignment created successfully!');
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

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
        $this->authorize('update', $assignment);
        return view('assignments.edit', compact('assignment'));
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $assignment->update($request->validated());

        return redirect()->route('assignments.show', $assignment)
                         ->with('success', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);
        $assignment->delete();

        return redirect()->route('courses.show', [$assignment->course->slug, 'tab' => 'assignments'])
                         ->with('success', 'Assignment deleted.');
    }
}
