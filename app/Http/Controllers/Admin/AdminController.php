<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiActionsLog;
use App\Models\Course;
use App\Models\Extension;
use App\Models\Submission;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_submissions' => Submission::count(),
            'pending_grading' => Submission::where('status', 'submitted')->count(),
            'ai_actions_today' => AiActionsLog::whereDate('created_at', today())->count(),
            'ai_actions_total' => AiActionsLog::count(),
            'extensions_active' => Extension::where('is_active', true)->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentAiActions = AiActionsLog::with('user')->latest()->take(10)->get();
        $recentCourses = Course::with('instructor')->withCount('students')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentAiActions', 'recentCourses'));
    }
}
