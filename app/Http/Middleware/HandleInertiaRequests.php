<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        if ($user && Schema::hasTable('announcements')) {
            try {
                $readIds = Schema::hasTable('announcement_reads')
                    ? DB::table('announcement_reads')
                        ->where('user_id', $user->id)
                        ->pluck('announcement_id')
                    : collect();

                $unreadAnnouncementsCount = Announcement::query()
                    ->whereNull('course_id')
                    ->when($readIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $readIds))
                    ->count();
            } catch (\Throwable) {
                $unreadAnnouncementsCount = 0;
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
            'csrfToken' => csrf_token(),
        ];
    }
}
