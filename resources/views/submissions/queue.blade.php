@extends('layouts.dashboard')
@section('title', 'Grading Queue')
@section('page-title', 'Grading Queue')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Grading Queue</h2>
            <p class="text-sm text-vl-ink-secondary tabular-nums">
                @if($pendingCount > 0)
                    <span class="font-semibold text-vl-gate">{{ $pendingCount }}</span> {{ \Illuminate\Support\Str::plural('submission', $pendingCount) }} awaiting evaluation
                @else
                    All caught up &mdash; no pending submissions
                @endif
            </p>
        </div>
    </div>

    {{-- Tabular KPI Strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-stat-card label="To Grade" :value="$stats['pending']" tone="warning" />
        <x-stat-card label="Late" :value="$stats['late']" tone="danger" />
        <x-stat-card label="Graded" :value="$stats['graded']" tone="success" />
        <x-stat-card label="Total" :value="$stats['total']" tone="info" />
    </div>

    {{-- Course Filter Pills --}}
    <div class="flex flex-wrap items-center gap-2">
        <a
            href="{{ route('submissions.queue') }}"
            class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 {{ !$courseFilter ? 'bg-vl-accent/14 text-vl-accent' : 'bg-vl-surface-raised/70 text-vl-ink-secondary hover:text-vl-ink' }}"
        >
            All Courses
        </a>
        @foreach($courses as $c)
            <a
                href="{{ route('submissions.queue', ['course' => $c->id]) }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 {{ $courseFilter == $c->id ? 'bg-vl-accent/14 text-vl-accent' : 'bg-vl-surface-raised/70 text-vl-ink-secondary hover:text-vl-ink' }}"
            >
                <span>{{ $c->title }}</span>
                @if(isset($perCoursePending[$c->id]) && $perCoursePending[$c->id] > 0)
                    <span class="rounded-md bg-vl-surface px-1.5 py-0.5 font-mono text-[10px] tabular-nums">{{ $perCoursePending[$c->id] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Status Filter --}}
    <div class="flex items-center gap-2">
        <span class="text-xs font-medium text-vl-ink-muted">Filter:</span>
        @foreach(['all' => 'All', 'pending' => 'Pending', 'graded' => 'Graded'] as $f => $label)
            <a
                href="{{ route('submissions.queue', array_merge(request()->only('course'), ['status' => $f])) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors duration-150 {{ $statusFilter === $f ? 'bg-vl-surface text-vl-ink shadow-sm' : 'text-vl-ink-secondary hover:text-vl-ink' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($submissions->isEmpty())
        <x-card :padding="true">
            <x-empty-state
                :title="$statusFilter === 'pending' ? 'No pending submissions' : 'No submissions found'"
                description="Submitted student code snapshots will appear in this queue automatically."
            />
        </x-card>
    @else
        <div class="space-y-6">
            @foreach($grouped as $assignmentId => $group)
                @php
                    $assignment = $group['assignment'];
                    $subs = $group['submissions'];
                @endphp
                <div class="space-y-2.5">
                    <div class="flex flex-wrap items-center justify-between gap-2 px-1">
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <a href="{{ route('assignments.show', $assignmentId) }}" class="font-bold text-vl-ink hover:text-vl-accent transition-colors">
                                {{ $assignment->title }}
                            </a>
                            <span class="text-vl-ink-muted">&middot;</span>
                            <span class="text-xs text-vl-ink-secondary">{{ $assignment->course->title }}</span>
                            @if($assignment->due_date)
                                <span class="text-vl-ink-muted">&middot;</span>
                                <span class="text-xs font-mono {{ $assignment->isOverdue() ? 'text-vl-danger font-semibold' : 'text-vl-ink-secondary' }}">
                                    Due {{ $assignment->due_date->format('M d') }}
                                </span>
                            @endif
                        </div>
                        <span class="font-mono text-xs text-vl-ink-muted tabular-nums">{{ $subs->count() }} {{ \Illuminate\Support\Str::plural('submission', $subs->count()) }}</span>
                    </div>

                    <div class="vl-panel divide-y divide-vl-border/10 overflow-hidden">
                        @foreach($subs as $sub)
                            <div class="px-5 py-3.5 flex flex-wrap items-center justify-between gap-4 hover:bg-vl-surface-raised/50 transition-colors duration-150">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="h-9 w-9 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($sub->student->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-vl-ink truncate">{{ $sub->student->name }}</p>
                                        <p class="text-xs text-vl-ink-muted">
                                            {{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : 'Not submitted' }}
                                            @if($sub->isLate())
                                                <span class="font-semibold text-vl-danger">&middot; Late</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    @if($sub->status === 'graded')
                                        @php $pct = $assignment->max_points > 0 ? ($sub->grade / $assignment->max_points) * 100 : 0; @endphp
                                        <span class="font-mono text-sm font-bold tabular-nums {{ $pct >= 80 ? 'text-vl-teal' : ($pct >= 60 ? 'text-vl-gate' : 'text-vl-danger') }}">
                                            {{ $sub->grade }}/{{ $assignment->max_points }}
                                        </span>
                                    @endif
                                    <x-badge :variant="$sub->status === 'graded' ? 'success' : ($sub->status === 'late' ? 'danger' : 'info')">
                                        {{ ucfirst($sub->status) }}
                                    </x-badge>
                                    @if(in_array($sub->status, ['submitted', 'late', 'graded']))
                                        <a href="{{ route('submissions.show', $sub->id) }}" class="{{ $sub->status === 'graded' ? 'vl-btn-secondary' : 'vl-btn-primary' }} !py-1.5 !px-3.5 !text-xs">
                                            {{ $sub->status === 'graded' ? 'Review' : 'Grade' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $submissions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
