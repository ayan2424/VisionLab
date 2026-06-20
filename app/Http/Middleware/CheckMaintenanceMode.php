<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't check for admin routes or login/logout
        if ($request->is('admin/*') || $request->is('login') || $request->is('logout') || $request->is('healthz')) {
            return $next($request);
        }

        $isMaintenanceMode = SystemConfig::getVal('maintenance_mode', false);

        if ($isMaintenanceMode) {
            // If user is admin, they can bypass maintenance mode
            if (Auth::check() && Auth::user()->isAdmin()) {
                return $next($request);
            }

            // Return maintenance view or JSON for API requests
            if ($request->expectsJson()) {
                return response()->json(['message' => 'System is currently undergoing maintenance. Please try again later.'], 503);
            }

            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
