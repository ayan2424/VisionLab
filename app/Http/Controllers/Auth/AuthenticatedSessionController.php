<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Block suspended accounts from logging in
        if (Auth::user()->status === 'suspended') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Please contact your administrator.']);
        }

        $request->session()->regenerate();

        // Update streak & Gamification
        $user = Auth::user();
        if (!$user->last_activity_at || $user->last_activity_at->lt(now()->startOfDay())) {
            // If last activity was exactly yesterday, increment. Otherwise, reset to 1.
            $streakMaintained = false;
            if ($user->last_activity_at && $user->last_activity_at->isYesterday()) {
                $user->current_streak += 1;
                $streakMaintained = true;
            } else {
                $user->current_streak = 1;
            }
            if ($user->current_streak > $user->longest_streak) {
                $user->longest_streak = $user->current_streak;
            }
            
            // Gamification: Daily Login XP
            $xpAward = 10; // Base XP for login
            if ($streakMaintained) {
                $xpAward += min($user->current_streak * 2, 50); // Bonus XP up to 50
            }
            
            $user->last_activity_at = now();
            $user->save();

            \App\Services\GamificationService::awardXpAndEvaluate($user, $xpAward, "Daily Login Streak ({$user->current_streak})");
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
