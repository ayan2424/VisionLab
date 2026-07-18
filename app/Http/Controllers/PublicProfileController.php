<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\DB;

class PublicProfileController extends Controller
{
    public function show($student_id)
    {
        $user = User::where('student_id', $student_id)->where('role', 'student')->firstOrFail();

        // 1. Tech Stack Aggregation (Workspaces by Language)
        $techStack = Workspace::where('student_id', $user->id)
            ->whereNotNull('language')
            ->select('language', DB::raw('count(*) as count'))
            ->groupBy('language')
            ->orderByDesc('count')
            ->get();

        // 2. Coding Schedule (Morning, Afternoon, Evening, Night)
        // We'll analyze recent AnalyticsEvents (e.g., workspace_started, login)
        $events = AnalyticsEvent::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();
            
        $schedule = [
            'Morning (6AM - 12PM)' => 0,
            'Afternoon (12PM - 6PM)' => 0,
            'Evening (6PM - 12AM)' => 0,
            'Night Owl (12AM - 6AM)' => 0,
        ];
        
        foreach ($events as $event) {
            $hour = $event->created_at->hour;
            if ($hour >= 6 && $hour < 12) $schedule['Morning (6AM - 12PM)']++;
            elseif ($hour >= 12 && $hour < 18) $schedule['Afternoon (12PM - 6PM)']++;
            elseif ($hour >= 18 && $hour <= 23) $schedule['Evening (6PM - 12AM)']++;
            else $schedule['Night Owl (12AM - 6AM)']++;
        }
        
        $totalEvents = $events->count() ?: 1; // Prevent division by zero
        $schedulePercentages = [];
        foreach ($schedule as $period => $count) {
            $schedulePercentages[$period] = round(($count / $totalEvents) * 100);
        }
        
        // Find the dominant coding time
        $dominantTime = array_keys($schedulePercentages, max($schedulePercentages))[0] ?? 'New Coder';

        // 3. General Stats
        $totalWorkspaces = Workspace::where('student_id', $user->id)->count();
        $totalXpTransactions = $user->xpTransactions()->count();
        $badges = $user->badges()->latest('earned_at')->get();
        
        // 4. Activity Heatmap Data
        $heatmapData = AnalyticsEvent::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(60))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        return view('portfolio.show', compact(
            'user', 
            'techStack', 
            'schedulePercentages', 
            'dominantTime', 
            'totalWorkspaces', 
            'badges',
            'heatmapData'
        ));
    }
}
