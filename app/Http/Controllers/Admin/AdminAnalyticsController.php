<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiActionsLog;
use App\Models\AiChatSession;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        $now = now();

        // Daily registrations for last 30 days
        $dailyUsers = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $userChart = $this->fillDays(29, $dailyUsers);

        // Daily submissions for last 30 days
        $dailySubs = Submission::select(
                DB::raw('DATE(submitted_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(submitted_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $submissionChart = $this->fillDays(29, $dailySubs);

        // Daily AI actions for last 30 days
        $dailyAi = AiActionsLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $aiChart = $this->fillDays(29, $dailyAi);

        // AI mode distribution
        $aiModes = AiChatSession::select('mode', DB::raw('COUNT(*) as count'))
            ->groupBy('mode')
            ->pluck('count', 'mode')
            ->toArray();

        // Summary stats
        $stats = [
            'total_users'       => User::count(),
            'total_courses'     => Course::count(),
            'total_submissions' => Submission::count(),
            'pending_grading'   => Submission::where('status', 'submitted')->count(),
            'ai_actions_today'  => AiActionsLog::whereDate('created_at', today())->count(),
            'ai_actions_total'  => AiActionsLog::count(),
        ];

        $recentActions = AiActionsLog::with('user')->latest()->take(10)->get();

        return view('admin.analytics.index', compact(
            'userChart', 'submissionChart', 'aiChart', 'aiModes', 'stats', 'recentActions'
        ));
    }

    private function fillDays(int $days, $data): array
    {
        $result = ['labels' => [], 'values' => []];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result['labels'][] = now()->subDays($i)->format('M d');
            $result['values'][] = $data->has($date) ? (int) $data[$date]->count : 0;
        }
        return $result;
    }
}
