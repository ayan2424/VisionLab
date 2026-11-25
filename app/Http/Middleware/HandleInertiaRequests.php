<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default on every Inertia response.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $unreadAnnouncementsCount = 0;
        $recentNotifications = [];
        if ($user && Schema::hasTable('announcements')) {
            try {
                $readIds = Schema::hasTable('announcement_reads')
                    ? DB::table('announcement_reads')
                        ->where('user_id', $user->id)
                        ->pluck('announcement_id')
                        ->all()
                    : [];

                $courseIds = collect();
                if (Schema::hasTable('courses')) {
                    if ($user->role === 'admin') {
                        $courseIds = DB::table('courses')->pluck('id');
                    } else {
                        $taught = DB::table('courses')->where('instructor_id', $user->id)->pluck('id');
                        $enrolled = Schema::hasTable('enrollments')
                            ? DB::table('enrollments')->where('student_id', $user->id)->pluck('course_id')
                            : collect();
                        $courseIds = $taught->merge($enrolled)->unique()->values();
                    }
                }

                $baseQuery = Announcement::query()
                    ->with('course:id,title,slug')
                    ->where(function ($q) use ($courseIds) {
                        $q->whereNull('course_id');
                        if ($courseIds->isNotEmpty()) {
                            $q->orWhereIn('course_id', $courseIds->all());
                        }
                    });

                $unreadAnnouncementsCount = (clone $baseQuery)
                    ->when(!empty($readIds), fn ($q) => $q->whereNotIn('id', $readIds))
                    ->count();

                $recentNotifications = (clone $baseQuery)
                    ->orderByDesc('pinned')
                    ->latest()
                    ->limit(6)
                    ->get()
                    ->map(fn (Announcement $a) => [
                        'id'            => $a->id,
                        'title'         => $a->title,
                        'body'          => Str::limit(strip_tags((string) $a->body), 120),
                        'pinned'        => (bool) $a->pinned,
                        'course_title'  => $a->course?->title,
                        'course_slug'   => $a->course?->slug,
                        'is_unread'     => !in_array($a->id, $readIds, true),
                        'created_human' => $a->created_at?->diffForHumans() ?? 'Just now',
                    ])
                    ->values()
                    ->all();
            } catch (\Throwable) {
                $unreadAnnouncementsCount = 0;
                $recentNotifications = [];
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'role'            => $user->role,
                    'status'          => $user->status ?? 'active',
                    'student_id'      => $user->student_id,
                    'avatar_initials' => $user->avatar_initials,
                    'xp'              => (int) ($user->xp ?? 0),
                    'level'           => (int) ($user->level ?? 1),
                    'rank_title'      => $user->rank_title ?? 'Novice',
                    'current_streak'  => (int) ($user->current_streak ?? 0),
                ] : null,
                'impersonating' => $request->session()->has('impersonator_id'),
            ],
            'flash' => [
                'status'  => fn () => $request->session()->get('status'),
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'unreadAnnouncementsCount' => $unreadAnnouncementsCount,
            'recentNotifications'      => $recentNotifications,
            'csrfToken' => csrf_token(),
        ];
    }
}
