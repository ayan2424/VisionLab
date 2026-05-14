<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders — Adds OWASP-recommended security headers to all responses.
 *
 * Registered in bootstrap/app.php as global middleware for production.
 * In development, some headers are relaxed to allow hot-reloading.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isProduction = app()->environment('production');

        // Prevent clickjacking — allow same-origin iframe (code-server)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(self), geolocation=(), payment=()');

        if ($isProduction) {
            // HSTS — enforce HTTPS for 1 year
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

            // Content Security Policy (relaxed for code-server iframe and CDNs)
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://js.pusher.com https://meet.jit.si https://8x8.vc",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
                "img-src 'self' data: blob: https:",
                "connect-src 'self' wss: ws: https://generativelanguage.googleapis.com https://api.piston.sh",
                "frame-src 'self' http://localhost:* https://meet.jit.si https://8x8.vc",
                "media-src 'self' blob:",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]);

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
