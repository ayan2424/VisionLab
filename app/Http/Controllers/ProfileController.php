<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserBadge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's gamified profile (Heatmap and Badges).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Ensure user level/rank is up to date (for students)
        if ($user->isStudent()) {
            $user->recalculateLevelAndRank();
            $user->save();
        }
        
        $badges = UserBadge::where('user_id', $user->id)
            ->orderByDesc('earned_at')
            ->get();

        // Fetch leaderboard (Top 10 active students by XP)
        $leaderboard = \App\Models\User::where('role', 'student')
            ->where('status', 'active')
            ->orderBy('xp', 'desc')
            ->take(10)
            ->get();
            
        // Fetch recent XP transactions for the user
        $transactions = $user->xpTransactions()->latest()->take(20)->get();
        
        // Compute progress to next level
        $currentLevelXp = pow($user->level - 1, 2) * 100;
        $nextLevelXp = pow($user->level, 2) * 100;
        $progressPercent = 0;
        if ($nextLevelXp > $currentLevelXp) {
            $progressPercent = min(100, max(0, (($user->xp - $currentLevelXp) / ($nextLevelXp - $currentLevelXp)) * 100));
        }

        return view('profile.index', [
            'user' => $user,
            'badges' => $badges,
            'leaderboard' => $leaderboard,
            'transactions' => $transactions,
            'nextLevelXp' => $nextLevelXp,
            'progressPercent' => $progressPercent,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
