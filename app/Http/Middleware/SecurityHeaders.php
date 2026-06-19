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
            // Allows scripts from self, unsafe-inline for dev, and storage.googleapis for Workbox
            // Jitsi needs specific frame-src or script-src depending on setup
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://storage.googleapis.com https://15.207.144.48:8443; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                   "font-src 'self' https://fonts.gstatic.com; " .
                   "img-src 'self' data: https: http:; " .
                   "connect-src 'self' ws: wss: https: http:; " .
                   "frame-src 'self' https://15.207.144.48:8443; " .
                   "object-src 'none';";
                   
            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
