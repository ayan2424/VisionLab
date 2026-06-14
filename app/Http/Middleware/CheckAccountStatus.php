<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckAccountStatus — Rejects requests from suspended users.
 *
 * Applied globally to all authenticated routes. If a user's status
 * becomes 'suspended' during an active session, their session is
 * invalidated and they are redirected to login with a clear message.
 */
class CheckAccountStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isSuspended()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with(
                'status',
                'Your account has been suspended. Please contact an administrator.'
            );
        }

        return $next($request);
    }
}
