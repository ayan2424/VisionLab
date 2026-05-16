<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\Room;
use App\Models\Submission;
use App\Policies\CoursePolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\WorkspacePolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Room::class, WorkspacePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }
}
