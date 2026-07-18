<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ensure user level/rank is up to date
        $user->recalculateLevelAndRank();
        $user->save();
        
        // Fetch leaderboard (Top 10 active students by XP)
        $leaderboard = User::where('role', 'student')
            ->where('status', 'active')
            ->orderBy('xp', 'desc')
            ->take(10)
            ->get();
            
        // Fetch recent XP transactions for the user
        $transactions = $user->xpTransactions()->latest()->take(20)->get();
        
        // Fetch user badges
        $badges = $user->badges()->latest('earned_at')->get();
        
        // Compute progress to next level
        $currentLevelXp = pow($user->level - 1, 2) * 100;
        $nextLevelXp = pow($user->level, 2) * 100;
        $progressPercent = 0;
        if ($nextLevelXp > $currentLevelXp) {
            $progressPercent = min(100, max(0, (($user->xp - $currentLevelXp) / ($nextLevelXp - $currentLevelXp)) * 100));
        }

        return view('gamification.index', compact('user', 'leaderboard', 'transactions', 'badges', 'nextLevelXp', 'progressPercent'));
    }
}
