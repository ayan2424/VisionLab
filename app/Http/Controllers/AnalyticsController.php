<?php

namespace App\Http\Controllers;

use App\Models\AiActionsLog;
use App\Models\AiChatSession;
use App\Models\AnalyticsEvent;
use App\Models\Submission;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics for the current user (Instructor or Student).
     * Students see their own analytics. Instructors see aggregated analytics for their courses.
     */
    public function index()
    {
        $user = Auth::user();
        $now = now();

        if ($user->isStudent()) {
            // Student view
            $executions = AnalyticsEvent::forUser($user->id)
                ->ofType('workspace.command_executed')
                ->count();
                
            $aiInteractions = AiActionsLog::where('user_id', $user->id)->count();
            
            $activeSessions = Workspace::where('user_id', $user->id)
                ->where('status', 'running')
                ->count();

            // Activity chart (Last 14 days)
            $dailyActivity = AnalyticsEvent::forUser($user->id)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('count', 'date');
                
            $activityChart = $this->fillDays(13, $dailyActivity);

            $data = compact('executions', 'aiInteractions', 'activeSessions', 'activityChart');
            $data['user'] = $user;

            return view('analytics.student', $data);
            
        } elseif ($user->isInstructor()) {
            // Instructor view
            $myCourses = $user->taughtCourses()->pluck('id');
            $studentIds = DB::table('enrollments')
                ->whereIn('course_id', $myCourses)
                ->where('status', 'active')
                ->pluck('student_id');

            $executions = AnalyticsEvent::whereIn('user_id', $studentIds)
                ->ofType('workspace.command_executed')
                ->count();
                
            $aiInteractions = AiActionsLog::whereIn('user_id', $studentIds)->count();
            
            $pendingGrading = Submission::whereIn('course_id', $myCourses)
                ->where('status', 'submitted')
                ->count();

            // Activity chart (Last 14 days)
            $dailyActivity = AnalyticsEvent::whereIn('user_id', $studentIds)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('count', 'date');
                
            $activityChart = $this->fillDays(13, $dailyActivity);

            $recentEvents = AnalyticsEvent::whereIn('user_id', $studentIds)
                ->with('user')
                ->latest()
                ->take(10)
                ->get();
            
            $data = compact('executions', 'aiInteractions', 'pendingGrading', 'activityChart', 'recentEvents');

            return view('analytics.instructor', $data);
        }

        // Admins should use AdminAnalyticsController
        return redirect()->route('admin.analytics');
    }

    private function fillDays(int $days, $data): array
    {
        $result = ['labels' => [], 'values' => []];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result['labels'][] = now()->subDays($i)->format('M d');
            $result['values'][] = $data->has($date) ? (int) $data[$date] : 0;
        }
        return $result;
    }
}
