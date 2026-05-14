<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Announcement;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isInstructor()) {
            return $this->instructorDashboard($user);
        }

        return $this->studentDashboard($user);
    }

    private function studentDashboard($user)
    {
        $courses = Course::whereHas('enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })->with('instructor')->withCount('students')->latest()->take(6)->get();

        $upcomingAssignments = Assignment::whereHas('course.enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })
        ->where('due_date', '>', now())
        ->orderBy('due_date')
        ->take(5)
        ->with('course')
        ->get();

        $recentAnnouncements = Announcement::whereHas('course.enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })->with(['course', 'author'])->latest()->take(5)->get();

        $pendingSubmissions = Submission::where('student_id', $user->id)
                                        ->whereIn('status', ['in_progress', 'not_started'])
                                        ->count();

        return view('dashboard.student', compact(
            'courses', 'upcomingAssignments', 'recentAnnouncements', 'pendingSubmissions'
        ));
    }

    private function instructorDashboard($user)
    {
        $courses = Course::where('instructor_id', $user->id)
                         ->withCount('students')
                         ->latest()
                         ->get();

        $pendingGrading = Submission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->where('status', 'submitted')->count();

        $totalStudents = $courses->sum('students_count');

        $recentSubmissions = Submission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->with(['student', 'assignment.course'])->latest()->take(10)->get();

        return view('dashboard.instructor', compact(
            'courses', 'pendingGrading', 'totalStudents', 'recentSubmissions'
        ));
    }
}
