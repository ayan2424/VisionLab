<?php

namespace App\Providers;

use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Fix asset URLs so @vite generates correct links ──────────
        // When running behind Replit's proxy the HTTP_HOST is the public
        // domain, not localhost. Forcing the root URL here ensures that
        // asset() / @vite produce URLs the browser can actually reach.
        if (isset($_SERVER['HTTP_HOST'])) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
                    || (($_SERVER['HTTP_X_FORWARDED_SSL']   ?? '') === 'on')
                    || str_contains($_SERVER['HTTP_HOST'], '.replit.dev')
                    || str_contains($_SERVER['HTTP_HOST'], '.replit.app');

            $scheme = $isHttps ? 'https' : 'http';
            URL::forceRootUrl($scheme . '://' . $_SERVER['HTTP_HOST']);
            if ($isHttps) {
                URL::forceScheme('https');
            }
        }

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Course::class, CoursePolicy::class);

        \App\Models\Submission::observe(\App\Observers\AnalyticsObserver::class);
        \App\Models\User::observe(\App\Observers\AnalyticsObserver::class);
        \App\Models\Workspace::observe(\App\Observers\AnalyticsObserver::class);

        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Phase 11 Specific Rate Limiters
        \Illuminate\Support\Facades\RateLimiter::for('auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('ai', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('file-api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('video', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('push', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('admin', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
