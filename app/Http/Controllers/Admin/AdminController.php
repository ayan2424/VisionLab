<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiActionsLog;
use App\Models\AiPendingPatch;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Extension;
use App\Models\Submission;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // ── Core Stats ──────────────────────────────────────────────────
        $stats = [
            'total_users'       => User::count(),
            'total_admins'      => User::where('role', 'admin')->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_students'    => User::where('role', 'student')->count(),
            'suspended_users'   => User::where('status', 'suspended')->count(),
            'total_courses'     => Course::count(),
            'active_courses'    => Course::where('is_active', true)->count(),
            'total_submissions' => Submission::count(),
            'pending_grading'   => Submission::whereIn('status', ['submitted', 'late'])->count(),
            'graded_today'      => Submission::where('status', 'graded')
                                    ->whereDate('updated_at', today())->count(),
        ];

        // ── AI Telemetry ────────────────────────────────────────────────
        $stats['ai_actions_today']   = AiActionsLog::whereDate('created_at', today())->count();
        $stats['ai_actions_total']   = AiActionsLog::count();
        $stats['ai_patches_pending'] = AiPendingPatch::where('status', 'pending')->count();
        $stats['ai_patches_total']   = AiPendingPatch::count();

        // ── Infrastructure ──────────────────────────────────────────────
        $stats['active_workspaces']  = Workspace::where('status', 'running')->count();
        $stats['total_workspaces']   = Workspace::count();
        $stats['extensions_active']  = Extension::where('is_active', true)->count();
        $stats['extensions_total']   = Extension::count();

        // ── Recent Activity ─────────────────────────────────────────────
        $recentUsers     = User::latest()->take(5)->get();
        $recentAiActions = AiActionsLog::with('user')->latest()->take(10)->get();
        $recentCourses   = Course::with('instructor')->withCount('students')->latest()->take(5)->get();
        $recentAuditLogs = AuditLog::with('actor')->latest()->take(10)->get();

        // ── Weekly Signup Trend (last 7 days) ───────────────────────────
        $signupTrend = User::selectRaw("DATE(created_at) as day, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats', 'recentUsers', 'recentAiActions', 'recentCourses',
            'recentAuditLogs', 'signupTrend'
        ));
    }
}
