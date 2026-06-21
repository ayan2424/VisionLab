<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinCourseRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function joinForm()
    {
        return view('enrollments.join');
    }

    public function joinByCode(JoinCourseRequest $request)
    {
        $validated = $request->validated();

        $course = Course::where('enrollment_code', $validated['enrollment_code'])
                        ->where('is_active', true)
                        ->first();

        if (!$course) {
            return back()->withErrors([
                'enrollment_code' => 'Invalid enrollment code. Please check and try again.',
            ])->withInput();
        }

        $user = Auth::user();

        // Check policy gate
        if (!$user->can('enroll', $course)) {
            return back()->withErrors([
                'enrollment_code' => 'You cannot enroll in this course.',
            ]);
        }

        $existing = Enrollment::where('course_id', $course->id)
                               ->where('student_id', $user->id)
                               ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return redirect()->route('courses.show', $course->slug)
                                 ->with('info', 'You are already enrolled in this course.');
            }
            // Re-activate dropped enrollment
            $existing->update(['status' => 'active', 'enrolled_at' => now()]);
        } else {
            Enrollment::create([
                'course_id'   => $course->id,
                'student_id'  => $user->id,
                'status'      => 'active',
                'enrolled_at' => now(),
            ]);
        }

        return redirect()->route('courses.show', $course->slug)
                         ->with('success', "Welcome to {$course->title}!");
    }

    public function invite(Request $request, Course $course)
    {
        $this->authorize('manageStudents', $course);

        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email address.']);
        }

        if (!$user->isStudent()) {
            return back()->withErrors(['email' => 'This user is not a student.']);
        }

        $existing = Enrollment::where('course_id', $course->id)
                              ->where('student_id', $user->id)
                              ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return back()->with('info', 'Student is already enrolled.');
            }
            $existing->update(['status' => 'active', 'enrolled_at' => now()]);
        } else {
            Enrollment::create([
                'course_id'   => $course->id,
                'student_id'  => $user->id,
                'status'      => 'invited', // Using invited status per requirements
                'enrolled_at' => now(),
            ]);
        }

        return back()->with('success', "Invitation sent to {$user->name}.");
    }

    public function remove(Course $course, int $studentId)
    {
        $this->authorize('manageStudents', $course);

        Enrollment::where('course_id', $course->id)
                  ->where('student_id', $studentId)
                  ->update(['status' => 'dropped']);

        return back()->with('success', 'Student removed from course.');
    }

    public function addByStudentId(Request $request, Course $course)
    {
        $this->authorize('manageStudents', $course);

        $request->validate([
            'student_id' => 'required|string|exists:users,student_id'
        ], [
            'student_id.exists' => 'No student found with this Student ID.'
        ]);

        $user = User::where('student_id', $request->student_id)->first();

        if (!$user->isStudent()) {
            return back()->withErrors(['student_id' => 'This user is not a student.']);
        }

        $existing = Enrollment::where('course_id', $course->id)
                              ->where('student_id', $user->id)
                              ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return back()->with('success', 'Student is already enrolled in this course.');
            }
            $existing->update(['status' => 'active', 'enrolled_at' => now()]);
        } else {
            Enrollment::create([
                'course_id'   => $course->id,
                'student_id'  => $user->id,
                'status'      => 'active',
                'enrolled_at' => now(),
            ]);
        }

        return back()->with('success', "Student {$user->name} successfully enrolled.");
    }

    public function importCsv(\App\Http\Requests\ImportEnrollmentCsvRequest $request, Course $course)
    {
        $this->authorize('manageStudents', $course);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        if (!$header || !in_array('email', array_map('strtolower', $header))) {
            return back()->withErrors(['csv_file' => 'CSV file must contain an "email" column.']);
        }
        
        $emailIndex = array_search('email', array_map('strtolower', $header));
        $roleIndex  = array_search('role', array_map('strtolower', $header)); // Optional

        $enrolledCount = 0;
        $createdCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $email = $row[$emailIndex] ?? null;
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $user = User::where('email', $email)->first();
            
            if (!$user) {
                // Auto-create user if they don't exist
                $role = ($roleIndex !== false && isset($row[$roleIndex])) ? strtolower($row[$roleIndex]) : 'student';
                $user = User::create([
                    'name' => explode('@', $email)[0],
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make('VisionLab2026!'),
                    'role' => in_array($role, ['student', 'instructor', 'admin']) ? $role : 'student',
                ]);
                $createdCount++;
            }

            // Enroll user
            $existing = Enrollment::where('course_id', $course->id)
                                  ->where('student_id', $user->id)
                                  ->first();

            if ($existing) {
                if ($existing->status !== 'active') {
                    $existing->update(['status' => 'active', 'enrolled_at' => now()]);
                    $enrolledCount++;
                }
            } else {
                Enrollment::create([
                    'course_id'   => $course->id,
                    'student_id'  => $user->id,
                    'status'      => 'active',
                    'enrolled_at' => now(),
                ]);
                $enrolledCount++;
            }
        }
        fclose($handle);

        return back()->with('success', "CSV Import Complete: $enrolledCount users enrolled. ($createdCount new accounts created).");
    }
}
