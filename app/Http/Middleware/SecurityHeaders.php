<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Security headers mapped to OWASP ASVS rules
        if (method_exists($response, 'header')) {
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

            // Set CSP - Content Security Policy
            // Allows scripts from self, unsafe-inline for dev, storage.googleapis for Workbox, and jsdelivr/unpkg/spline for 3D graphics
            $allowedScripts = "https://storage.googleapis.com https://15.207.144.48:8443 https://cdn.jsdelivr.net https://unpkg.com";
            $allowedStyles = "https://fonts.googleapis.com https://unpkg.com https://api.fontshare.com";
            $allowedConnect = "'self' ws: wss: https: http: https://prod.spline.design";

            // Whitelist Vite Dev Server in local development environment to prevent dashboard/login styling crashes
            if (app()->environment('local')) {
                $allowedScripts .= " http://localhost:5173 http://127.0.0.1:5173";
                $allowedStyles .= " http://localhost:5173 http://127.0.0.1:5173";
                $allowedConnect .= " ws://localhost:5173 ws://127.0.0.1:5173 http://localhost:5173 http://127.0.0.1:5173";
            }

            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " . $allowedScripts . "; " .
                   "style-src 'self' 'unsafe-inline' " . $allowedStyles . "; " .
                   "font-src 'self' https://fonts.gstatic.com https://cdn.fontshare.com; " .
                   "img-src 'self' data: blob: https: http:; " .
                   "connect-src " . $allowedConnect . "; " .
                   "frame-src 'self' https://15.207.144.48:8443 https://prod.spline.design; " .
                   "worker-src 'self' blob:; " .
                   "child-src 'self' blob:; " .
                   "object-src 'none';";
                   
            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
