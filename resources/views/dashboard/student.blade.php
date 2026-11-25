@extends('layouts.dashboard')
@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header Row --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2.5">
                <h2 class="text-2xl font-bold tracking-tight text-vl-ink">
                    Welcome back, {{ explode(' ', Auth::user()->name)[0] }}
                </h2>
                @if(Auth::user()->student_id)
                    <span class="rounded-lg bg-vl-surface-raised px-2.5 py-0.5 font-mono text-xs font-semibold text-vl-ink-secondary" title="Your Institutional Student ID">
                        {{ Auth::user()->student_id }}
                    </span>
                @endif
            </div>
            <p class="text-sm text-vl-ink-secondary">
                {{ now()->format('l, F j, Y') }} &middot; {{ $courses->count() }} active {{ \Illuminate\Support\Str::plural('course', $courses->count()) }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('courses.index') }}" class="vl-btn-secondary !py-2 !px-4 !text-xs sm:!text-sm">
                Browse Courses
            </a>
            <a href="{{ route('workspace.index') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Launch IDE
            </a>
        </div>
    </div>

    {{-- Tabular KPI Strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <x-stat-card label="Active Courses" :value="$courses->count()" tone="accent" />
        <x-stat-card label="Pending Submissions" :value="$pendingSubmissions ?? 0" tone="info" />
        <x-stat-card label="Due Soon" :value="isset($upcomingAssignments) ? $upcomingAssignments->count() : 0" tone="warning" />
        <x-stat-card label="Unread Notices" :value="$unreadAnnouncementCount ?? 0" tone="neutral" />
        <x-stat-card label="Day Streak" :value="($streak ?? 0) . 'd'" tone="success" />
    </div>

    {{-- Main Workspace Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-8 space-y-6">
            <x-card title="Active Courses" :padding="true">
                <x-slot:actions>
                    <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-vl-accent hover:underline">View all</a>
                </x-slot:actions>

                @if($courses->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($courses->take(6) as $course)
                            @php
                                $tones = ['bg-vl-accent/14 text-vl-accent', 'bg-vl-teal/14 text-vl-teal', 'bg-vl-gate/14 text-vl-gate'];
                                $toneClass = $tones[$loop->index % count($tones)];
                            @endphp
                            <a href="{{ route('courses.show', $course->slug) }}" class="flex items-center gap-3.5 rounded-xl p-3 bg-vl-surface-raised/65 hover:bg-vl-surface-raised transition-colors duration-150">
                                <div class="h-10 w-10 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold {{ $toneClass }}">
                                    {{ strtoupper(substr($course->title, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $course->title }}</p>
                                    <p class="text-xs text-vl-ink-muted truncate">{{ $course->instructor->name ?? 'Assigned Instructor' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-empty-state
                        title="No courses enrolled yet"
                        description="Use your instructor's course join code to enroll and provision your class workspaces."
                    >
                        <a href="{{ route('enrollments.join') }}" class="vl-btn-primary !py-2 !px-4 !text-xs">Join a Course</a>
                    </x-empty-state>
                @endif
            </x-card>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-card title="Recent Announcements" :padding="true">
                    <x-slot:actions>
                        <a href="{{ route('announcements.index') }}" class="text-xs font-semibold text-vl-accent hover:underline">All</a>
                    </x-slot:actions>

                    @if(isset($recentAnnouncements) && $recentAnnouncements->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentAnnouncements->take(4) as $announcement)
                                <a href="{{ route('announcements.index') }}" class="flex items-start gap-3 rounded-xl p-2.5 hover:bg-vl-surface-raised/70 transition-colors duration-150">
                                    <span class="mt-1.5 h-2 w-2 rounded-full shrink-0 {{ !$announcement->is_read ? 'bg-vl-accent' : 'bg-vl-border/25' }}"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-vl-ink truncate">{{ $announcement->title }}</p>
                                        <p class="text-xs text-vl-ink-muted mt-0.5">{{ $announcement->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-vl-ink-muted py-4 text-center">No recent announcements.</p>
                    @endif
                </x-card>

                <x-card title="Recent Grades" :padding="true">
                    <x-slot:actions>
                        <a href="{{ route('progress.index') }}" class="text-xs font-semibold text-vl-accent hover:underline">Progress</a>
                    </x-slot:actions>

                    @if(isset($gradedSubmissions) && $gradedSubmissions->count() > 0)
                        <div class="space-y-4">
                            @foreach($gradedSubmissions->take(4) as $sub)
                                @php
                                    $pct = ($sub->assignment && $sub->assignment->max_points > 0)
                                        ? (int) round(($sub->grade / $sub->assignment->max_points) * 100)
                                        : 0;
                                    $barTone = $pct >= 80 ? 'bg-vl-teal' : ($pct >= 50 ? 'bg-vl-gate' : 'bg-vl-danger');
                                    $textTone = $pct >= 80 ? 'text-vl-teal' : ($pct >= 50 ? 'text-vl-gate' : 'text-vl-danger');
                                @endphp
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between gap-2 text-sm">
                                        <span class="font-medium text-vl-ink truncate">{{ $sub->assignment->title ?? 'Assignment' }}</span>
                                        <span class="font-mono text-xs font-semibold tabular-nums {{ $textTone }}">{{ $pct }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-vl-surface-raised overflow-hidden">
                                        <div class="h-full rounded-full {{ $barTone }}" style="width: {{ min(100, max(0, $pct)) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-vl-ink-muted py-4 text-center">No graded submissions yet.</p>
                    @endif
                </x-card>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <x-card title="Upcoming Deadlines" :padding="true">
                @if(isset($upcomingAssignments) && $upcomingAssignments->count() > 0)
                    <div class="space-y-2.5">
                        @foreach($upcomingAssignments->take(5) as $assignment)
                            @php
                                $daysLeft = now()->diffInDays($assignment->due_date, false);
                                $variant = $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'success');
                                $label = $daysLeft < 0 ? 'Overdue' : ($daysLeft === 0 ? 'Today' : ($daysLeft === 1 ? 'Tomorrow' : $daysLeft . 'd left'));
                            @endphp
                            <a href="{{ route('courses.show', [$assignment->course->slug ?? '', 'tab' => 'assignments']) }}" class="flex items-center justify-between gap-3 rounded-xl p-2.5 hover:bg-vl-surface-raised/70 transition-colors duration-150">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-vl-ink truncate">{{ $assignment->title }}</p>
                                    <p class="text-xs text-vl-ink-muted truncate">{{ $assignment->course->title ?? '' }}</p>
                                </div>
                                <x-badge :variant="$variant" class="shrink-0 tabular-nums">{{ $label }}</x-badge>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-vl-ink-muted py-4 text-center">All caught up on deadlines.</p>
                @endif
            </x-card>

            <x-card title="Quick Actions" :padding="true">
                <div class="space-y-2">
                    <a href="{{ route('workspace.create') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>New Sandbox Workspace</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                    <a href="{{ route('enrollments.join') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>Join Course with Code</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                    <a href="{{ route('gamification.index') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>View Badges &amp; XP</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                </div>
            </x-card>

            <div class="vl-panel p-5 space-y-2">
                <div class="flex items-center gap-2.5 text-vl-accent">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    <p class="text-xs font-semibold text-vl-ink">AI Patch Governance Tip</p>
                </div>
                <p class="text-xs leading-relaxed text-vl-ink-secondary">
                    Inside your IDE, use Socratic Mode to trace failing assertions step by step, and review every proposed diff in the pending patch queue before committing changes.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
