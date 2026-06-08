<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ── KPI cards ────────────────────────────────────────────
        $totalUsers       = User::count();
        $rolesCount       = User::select('role', DB::raw('count(*) as count'))
                                ->groupBy('role')
                                ->pluck('count', 'role');
        $adminCount       = $rolesCount['admin'] ?? 0;
        $instructorCount  = $rolesCount['instructor'] ?? 0;
        $studentCount     = $rolesCount['student'] ?? 0;

        // ── Simulated metrics (realistic for demo) ───────────────
        $executions       = 1_247;
        $aiInteractions   = 834;
        $activeSessions   = rand(3, 12);
        $avgExecTime      = 1.34;

        // ── Activity chart — last 14 days (simulated) ────────────
        $activityLabels = [];
        $execData       = [];
        $aiData         = [];
        $collabData     = [];

        for ($i = 13; $i >= 0; $i--) {
            $date             = now()->subDays($i);
            $activityLabels[] = $date->format('M d');
            $execData[]       = rand(40, 180);
            $aiData[]         = rand(20, 90);
            $collabData[]     = rand(5, 40);
        }

        // ── Language distribution ─────────────────────────────────
        $languageLabels = ['Python', 'JavaScript', 'PHP', 'TypeScript', 'Java', 'Rust', 'Other'];
        $languageCounts = [38, 24, 15, 10, 7, 4, 2];

        // ── Recent sessions (last 10 — simulated) ─────────────────
        $languages  = ['python', 'javascript', 'typescript', 'php', 'java', 'rust'];
        $langIcons  = ['🐍', '⚡', '📘', '🐘', '☕', '🦀'];
        $recentSessions = [];
        $recentUsers = User::limit(8)->get();
        foreach ($recentUsers as $u) {
            $li = array_rand($languages);
            $recentSessions[] = [
                'user'       => $u,
                'language'   => $languages[$li],
                'icon'       => $langIcons[$li],
                'executions' => rand(3, 47),
                'ai_calls'   => rand(1, 28),
                'duration'   => rand(8, 142) . ' min',
                'ago'        => rand(1, 120) . 'm ago',
            ];
        }

        // ── Hourly heatmap (last 7 days × 24 hours) ──────────────
        $heatmap = [];
        for ($d = 6; $d >= 0; $d--) {
            $row = ['day' => now()->subDays($d)->format('D'), 'hours' => []];
            for ($h = 0; $h < 24; $h++) {
                $intensity = ($h >= 9 && $h <= 22) ? rand(0, 100) : rand(0, 20);
                $row['hours'][] = $intensity;
            }
            $heatmap[] = $row;
        }

        return view('analytics', compact(
            'totalUsers', 'adminCount', 'instructorCount', 'studentCount',
            'executions', 'aiInteractions', 'activeSessions', 'avgExecTime',
            'activityLabels', 'execData', 'aiData', 'collabData',
            'languageLabels', 'languageCounts',
            'recentSessions', 'heatmap'
        ));
    }
}
