<?php

namespace App\Http\Controllers;

use App\Models\AiActionsLog;
use App\Models\Deployment;
use App\Models\Submission;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ContributionController — Provides 365-day contribution heatmap data
 * and activity metrics for user profiles.
 */
class ContributionController extends Controller
{
    /** GET /api/contributions — 365-day heatmap data for the authenticated user */
    public function heatmap(Request $request): JsonResponse
    {
        $user = Auth::user();
        $targetId = $request->query('user_id', $user->id);

        // Only admins can view other users' heatmaps
        if ((int) $targetId !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $startDate = now()->subDays(365)->startOfDay();

        // Gather submission activity
        $submissions = Submission::where('student_id', $targetId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        // Gather AI interactions
        $aiActions = AiActionsLog::where('user_id', $targetId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->groupBy('date')
            ->pluck('count', 'date');

        // Merge into single heatmap
        $heatmap = [];
        for ($i = 365; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $submissionCount = $submissions[$date] ?? 0;
            $aiCount = $aiActions[$date] ?? 0;
            $total = $submissionCount + $aiCount;

            $heatmap[] = [
                'date'        => $date,
                'count'       => $total,
                'submissions' => $submissionCount,
                'ai_actions'  => $aiCount,
                'level'       => $this->intensityLevel($total),
            ];
        }

        // Streak calculation
        $streak = 0;
        $maxStreak = 0;
        for ($i = count($heatmap) - 1; $i >= 0; $i--) {
            if ($heatmap[$i]['count'] > 0) {
                $streak++;
                $maxStreak = max($maxStreak, $streak);
            } else {
                $streak = 0;
            }
        }

        return response()->json([
            'heatmap'     => $heatmap,
            'current_streak' => $streak,
            'max_streak'     => $maxStreak,
            'total_contributions' => array_sum(array_column($heatmap, 'count')),
        ]);
    }

    private function intensityLevel(int $count): int
    {
        return match (true) {
            $count === 0 => 0,
            $count <= 2  => 1,
            $count <= 5  => 2,
            $count <= 10 => 3,
            default      => 4,
        };
    }
}
