<?php

namespace App\Http\Controllers;

use App\Models\AiPendingPatch;
use App\Models\Assignment;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Submission;
use App\Models\UserBadge;
use App\Models\Workspace;
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

        // Upcoming deadlines across all enrolled courses
        $upcomingAssignments = Assignment::whereHas('course.enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })
        ->where('due_date', '>', now())
        ->orderBy('due_date')
        ->take(5)
        ->with('course')
        ->get();

        // Recent announcements with unread indicator
        $recentAnnouncements = Announcement::whereHas('course.enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })
        ->with(['course', 'author'])
        ->latest()
        ->take(5)
        ->get()
        ->each(function ($announcement) use ($user) {
            $announcement->is_unread = !$announcement->isReadBy($user);
        });

        $unreadAnnouncementCount = Announcement::whereHas('course.enrollments', function ($q) use ($user) {
            $q->where('student_id', $user->id)->where('status', 'active');
        })->whereDoesntHave('reads', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        // Submission stats
        $pendingSubmissions = Submission::where('student_id', $user->id)
                                        ->whereIn('status', ['in_progress', 'not_started'])
                                        ->count();

        $gradedSubmissions = Submission::where('student_id', $user->id)
                                       ->where('status', 'graded')
                                       ->latest('updated_at')
                                       ->take(3)
                                       ->with('assignment.course')
                                       ->get();

        // Streak and badges
        $streak = $user->current_streak ?? 0;
        $badges = UserBadge::where('user_id', $user->id)->latest('earned_at')->take(5)->get();

        // Active workspaces
        $activeWorkspaces = Workspace::where('student_id', $user->id)
                                      ->where('status', 'running')
                                      ->with('course')
                                      ->take(3)
                                      ->get();

        $data = compact(
            'courses', 'upcomingAssignments', 'recentAnnouncements',
            'unreadAnnouncementCount', 'pendingSubmissions', 'gradedSubmissions',
            'streak', 'badges', 'activeWorkspaces'
        );

        return view('dashboard.student', $data);
    }

    private function instructorDashboard($user)
    {
        $courses = Course::where('instructor_id', $user->id)
                         ->withCount('students')
                         ->latest()
                         ->get();

        // Pending grading count (submitted + late)
        $pendingGrading = Submission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->whereIn('status', ['submitted', 'late'])->count();

        $totalStudents = $courses->sum('students_count');

        // Recent submissions awaiting review
        $recentSubmissions = Submission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->with(['student', 'assignment.course'])->latest()->take(10)->get();

        // Late submission count
        $lateSubmissions = Submission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->where('status', 'late')->count();

        // Active workspaces in instructor's courses
        $activeWorkspaceCount = Workspace::whereHas('course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->where('status', 'running')->count();

        // Average grade across all graded submissions
        $avgGrade = Submission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->where('status', 'graded')->avg('grade');

        // AI activity — pending patches across instructor's courses
        $pendingPatches = AiPendingPatch::where('status', 'pending')
            ->whereHas('workspace.course', function ($q) use ($user) {
                $q->where('instructor_id', $user->id);
            })->count();

        $data = compact(
            'courses', 'pendingGrading', 'totalStudents', 'recentSubmissions',
            'lateSubmissions', 'activeWorkspaceCount', 'avgGrade', 'pendingPatches'
        );

        return view('dashboard.instructor', $data);
    }
}
