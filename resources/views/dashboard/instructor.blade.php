@extends('layouts.dashboard')
@section('title', 'Instructor Dashboard')
@section('page-title', 'Teaching Overview')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">
                Welcome back, {{ explode(' ', Auth::user()->name)[0] }}
            </h2>
            <p class="text-sm text-vl-ink-secondary">
                {{ now()->format('l, F j, Y') }} &middot; Course delivery, submissions, and student workspaces
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('submissions.queue') }}" class="vl-btn-secondary !py-2 !px-4 !text-xs sm:!text-sm">
                Grading Queue
            </a>
            <a href="{{ route('courses.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Course
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="My Courses" :value="$courses->count()" tone="accent" />
        <x-stat-card label="Total Students" :value="$totalStudents ?? 0" tone="info" />
        <x-stat-card label="Pending Grading" :value="$pendingGrading ?? 0" :tone="($pendingGrading ?? 0) > 0 ? 'warning' : 'neutral'" />
        <x-stat-card label="Average Class Grade" :value="($avgGrade ?? '—') . (is_numeric($avgGrade ?? null) ? '%' : '')" tone="success" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="vl-panel px-5 py-4 flex items-center justify-between">
            <span class="text-xs font-medium text-vl-ink-secondary">Late Submissions</span>
            <span class="font-mono text-lg font-bold tabular-nums {{ ($lateSubmissions ?? 0) > 0 ? 'text-vl-danger' : 'text-vl-ink' }}">{{ $lateSubmissions ?? 0 }}</span>
        </div>
        <div class="vl-panel px-5 py-4 flex items-center justify-between">
            <span class="text-xs font-medium text-vl-ink-secondary">Active Workspaces</span>
            <span class="font-mono text-lg font-bold tabular-nums text-vl-accent">{{ $activeWorkspaceCount ?? 0 }}</span>
        </div>
        <div class="vl-panel px-5 py-4 flex items-center justify-between">
            <span class="text-xs font-medium text-vl-ink-secondary">AI Patches Pending</span>
            <span class="font-mono text-lg font-bold tabular-nums text-vl-gate">{{ $pendingPatches ?? 0 }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-8 space-y-6">
            <x-card title="Active Courses" :padding="true">
                <x-slot:actions>
                    <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-vl-accent hover:underline">Manage all</a>
                </x-slot:actions>

                @if($courses->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($courses->take(6) as $course)
                            <a href="{{ route('courses.show', $course->slug) }}" class="flex items-center justify-between gap-3 rounded-xl p-3.5 bg-vl-surface-raised/65 hover:bg-vl-surface-raised transition-colors duration-150">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="h-10 w-10 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($course->title, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-vl-ink truncate">{{ $course->title }}</p>
                                        <p class="text-xs text-vl-ink-muted tabular-nums">
                                            {{ $course->enrollments_count ?? $course->enrollments()->count() }} students
                                        </p>
                                    </div>
                                </div>
                                @if($course->enrollment_code)
                                    <span class="font-mono text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-vl-surface text-vl-ink-secondary shrink-0">
                                        {{ $course->enrollment_code }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <x-empty-state
                        title="No courses created yet"
                        description="Create your first course to distribute assignments, extension policies, and isolated workspaces."
                    >
                        <a href="{{ route('courses.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs">Create Course</a>
                    </x-empty-state>
                @endif
            </x-card>

            <x-card title="Recent Submissions" :padding="true">
                <x-slot:actions>
                    <a href="{{ route('grading.queue') }}" class="text-xs font-semibold text-vl-accent hover:underline">Open Queue</a>
                </x-slot:actions>

                @if(isset($recentSubmissions) && $recentSubmissions->count() > 0)
                    <div class="divide-y divide-vl-border/10">
                        @foreach($recentSubmissions->take(6) as $sub)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="h-8 w-8 rounded-xl shrink-0 bg-vl-surface-raised flex items-center justify-center text-xs font-bold text-vl-ink">
                                        {{ strtoupper(substr($sub->student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-vl-ink truncate">{{ $sub->student->name ?? 'Student' }}</p>
                                        <p class="text-xs text-vl-ink-muted truncate">{{ $sub->assignment->title ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <x-badge :variant="$sub->grade !== null ? 'success' : 'info'">
                                        {{ $sub->grade !== null ? 'Graded' : 'Submitted' }}
                                    </x-badge>
                                    <a href="{{ route('submissions.show', $sub->id) }}" class="text-xs font-semibold text-vl-accent hover:underline">Inspect</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-vl-ink-muted py-4 text-center">No student submissions received yet.</p>
                @endif
            </x-card>
        </div>

        <div class="lg:col-span-4 space-y-6">
            @if(($pendingGrading ?? 0) > 0)
                <div class="vl-panel p-5 space-y-3 bg-vl-gate/10">
                    <div class="flex items-center gap-2 text-vl-gate">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p class="text-xs font-bold">Submissions Awaiting Review</p>
                    </div>
                    <p class="text-xs leading-relaxed text-vl-ink-secondary">
                        You have <strong class="font-semibold text-vl-ink tabular-nums">{{ $pendingGrading }}</strong> {{ \Illuminate\Support\Str::plural('submission', $pendingGrading) }} ready to grade.
                    </p>
                    <a href="{{ route('grading.queue') }}" class="vl-btn-primary !py-2 !px-4 !text-xs">
                        Review Queue &rarr;
                    </a>
                </div>
            @endif

            <x-card title="Instructor Shortcuts" :padding="true">
                <div class="space-y-2">
                    <a href="{{ route('courses.create') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>Create New Course</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                    <a href="{{ route('grading.queue') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>Open Grading Queue</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                    <a href="{{ route('announcements.create') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>Publish Announcement</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                    <a href="{{ route('workspace.index') }}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-vl-ink bg-vl-surface-raised/70 hover:bg-vl-surface-raised transition-colors duration-150">
                        <span>Launch Instructor IDE</span>
                        <span class="font-mono text-xs text-vl-accent">&rarr;</span>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
