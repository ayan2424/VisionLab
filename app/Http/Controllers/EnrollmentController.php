<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function joinForm()
    {
        return view('enrollments.join');
    }

    public function joinByCode(Request $request)
    {
        $request->validate([
            'enrollment_code' => 'required|string|max:10',
        ]);

        $course = Course::where('enrollment_code', strtoupper($request->enrollment_code))
                        ->where('is_active', true)
                        ->first();

        if (!$course) {
            return back()->withErrors(['enrollment_code' => 'Invalid enrollment code. Please check and try again.'])->withInput();
        }

        $user = Auth::user();

        if (!$user->isStudent()) {
            return back()->withErrors(['enrollment_code' => 'Only students can enroll in courses.']);
        }

        $existing = Enrollment::where('course_id', $course->id)
                               ->where('student_id', $user->id)
                               ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return redirect()->route('courses.show', $course->slug)
                                 ->with('info', 'You are already enrolled in this course.');
            }
            $existing->update(['status' => 'active']);
        } else {
            Enrollment::create([
                'course_id'  => $course->id,
                'student_id' => $user->id,
                'status'     => 'active',
                'enrolled_at'=> now(),
            ]);
        }

        return redirect()->route('courses.show', $course->slug)
                         ->with('success', "Welcome to {$course->title}!");
    }

    public function remove(Course $course, int $studentId)
    {
        $this->authorize('update', $course);

        Enrollment::where('course_id', $course->id)
                  ->where('student_id', $studentId)
                  ->delete();

        return back()->with('success', 'Student removed from course.');
    }
}
