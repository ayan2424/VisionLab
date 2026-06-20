<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiActionsLog;
use App\Models\AiChatSession;
use App\Models\AnalyticsEvent;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        $now = now();

        // 1. Daily Registrations (Last 30 Days)
        $dailyUsers = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date');
        $userChart = $this->fillDays(29, $dailyUsers);

        // 2. Daily Submissions (Last 30 Days)
        $dailySubs = Submission::select(DB::raw('DATE(submitted_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(submitted_at)'))
            ->pluck('count', 'date');
        $submissionChart = $this->fillDays(29, $dailySubs);

        // 3. Daily AI Actions (Last 30 Days)
        $dailyAi = AiActionsLog::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date');
        $aiChart = $this->fillDays(29, $dailyAi);

        // 4. AI Mode Distribution
        $aiModes = AiChatSession::select('mode', DB::raw('COUNT(*) as count'))
            ->groupBy('mode')
            ->pluck('count', 'mode')
            ->toArray();

        // 5. Workspace Activity (Last 14 Days)
        $dailyWorkspaces = AnalyticsEvent::ofType('workspace.started')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date');
        $workspaceChart = $this->fillDays(13, $dailyWorkspaces);

        // 6. Event Taxonomy Distribution
        $eventTypes = AnalyticsEvent::select('event_type', DB::raw('COUNT(*) as count'))
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'event_type')
            ->toArray();

        // 7. Active Sessions / Users by Role
        $roleDistribution = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Summary stats
        $stats = [
            'total_users'       => User::count(),
            'total_courses'     => Course::count(),
            'total_workspaces'  => Workspace::count(),
            'total_submissions' => Submission::count(),
            'pending_grading'   => Submission::where('status', 'submitted')->count(),
            'ai_actions_today'  => AiActionsLog::whereDate('created_at', today())->count(),
            'ai_actions_total'  => AiActionsLog::count(),
            'telemetry_events'  => AnalyticsEvent::count(),
        ];

        $recentEvents = AnalyticsEvent::with('user')->latest()->take(10)->get();

        return view('admin.analytics.index', compact(
            'userChart', 'submissionChart', 'aiChart', 'aiModes', 'workspaceChart', 'eventTypes', 'roleDistribution', 'stats', 'recentEvents'
        ));
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
