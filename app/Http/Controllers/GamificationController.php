<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    /**
     * Display the Gamification Dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Load gamification relations
        $user->load(['badges' => function ($query) {
            $query->orderBy('earned_at', 'desc');
        }, 'xpTransactions' => function ($query) {
            $query->orderBy('created_at', 'desc')->take(10);
        }]);

        // Calculate progress to next level
        // Level formula from User.php: Level = floor(sqrt(XP / 100)) + 1
        // Thus, XP required for a given level L is: XP = ((L - 1) ^ 2) * 100
        $currentLevel = $user->level > 0 ? $user->level : 1;
        $nextLevel = $currentLevel + 1;
        
        $xpForCurrentLevel = pow($currentLevel - 1, 2) * 100;
        $xpForNextLevel = pow($nextLevel - 1, 2) * 100;
        
        $xpIntoLevel = $user->xp - $xpForCurrentLevel;
        $xpNeededForNextLevel = $xpForNextLevel - $xpForCurrentLevel;
        
        $progressPercentage = $xpNeededForNextLevel > 0 
            ? min(100, max(0, round(($xpIntoLevel / $xpNeededForNextLevel) * 100))) 
            : 100;

        // Fetch user's workspaces count for stats
        $totalWorkspaces = \App\Models\AnalyticsEvent::where('user_id', $user->id)
            ->where('event_type', 'workspace_started')
            ->count();

        // Pass to view
        return view('gamification.index', compact(
            'user', 
            'currentLevel', 
            'nextLevel', 
            'xpForNextLevel', 
            'progressPercentage',
            'totalWorkspaces'
        ));
    }
}
