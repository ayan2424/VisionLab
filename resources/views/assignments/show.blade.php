@extends('layouts.dashboard')
@section('title', $assignment->title)
@section('page-title', 'Assignment Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="vl-panel p-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="space-y-2 min-w-0">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-vl-ink-muted" aria-label="Breadcrumb">
                <a href="{{ route('courses.index') }}" class="hover:text-vl-ink transition-colors">Courses</a>
                <span>/</span>
                <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-vl-ink transition-colors">{{ $course->title }}</a>
                <span>/</span>
                <span class="text-vl-ink-secondary">{{ $assignment->title }}</span>
            </nav>

            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">{{ $assignment->title }}</h2>

            <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-vl-ink-secondary tabular-nums">
                @if($assignment->due_date)
                    <span class="{{ $assignment->isOverdue() ? 'font-semibold text-vl-danger' : '' }}">
                        Due {{ $assignment->due_date->format('M d, Y · H:i') }}
                        @if(!$assignment->isOverdue())
                            ({{ $assignment->time_remaining }})
                        @endif
                    </span>
                @endif
                <span>{{ $assignment->max_points }} points</span>
                <x-badge variant="info">{{ ucfirst($assignment->starter_language) }}</x-badge>
            </div>
        </div>

        @if($isInstructor)
            <a href="{{ route('assignments.edit', $assignment->id) }}" class="vl-btn-secondary !py-2 !px-4 !text-xs shrink-0">Edit Assignment</a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="lg:col-span-8 space-y-6">
            @if($assignment->description)
                <x-card title="Instructions" :padding="true">
                    <div class="text-sm leading-relaxed text-vl-ink-secondary whitespace-pre-wrap">{{ $assignment->description }}</div>
                </x-card>
            @endif

            @if($assignment->starter_code)
                <x-card title="Starter Code" :padding="true">
                    <pre class="vl-code-stage p-4 font-mono text-xs leading-6 overflow-x-auto text-slate-200">{{ $assignment->starter_code }}</pre>
                </x-card>
            @endif

            @if($isInstructor)
                <x-card :title="'Submissions (' . $submissions->count() . ')'" :padding="true">
                    @php $pending = $submissions->whereIn('status', ['submitted', 'late'])->count(); @endphp
                    @if($pending > 0)
                        <x-slot:actions>
                            <x-badge variant="warning" class="tabular-nums">{{ $pending }} to grade</x-badge>
                        </x-slot:actions>
                    @endif

                    <div class="divide-y divide-vl-border/10">
                        @forelse($submissions as $sub)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="h-8 w-8 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($sub->student->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-vl-ink truncate">{{ $sub->student->name }}</p>
                                        <p class="text-xs text-vl-ink-muted tabular-nums">
                                            {{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : 'Not submitted' }}
                                            @if($sub->isLate())
                                                <span class="text-vl-danger font-medium">&middot; Late</span>
                                            @endif
                                            @if($sub->status === 'graded')
                                                <span class="text-vl-teal font-medium">&middot; {{ $sub->grade }}/{{ $assignment->max_points }} pts</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <x-badge :variant="$sub->status === 'graded' ? 'success' : ($sub->status === 'late' ? 'danger' : 'info')">
                                        {{ ucfirst($sub->status) }}
                                    </x-badge>
                                    @if(in_array($sub->status, ['submitted', 'late', 'graded']))
                                        <a href="{{ route('submissions.show', $sub->id) }}" class="vl-btn-secondary !py-1.5 !px-3 !text-xs">
                                            {{ $sub->status === 'graded' ? 'Review' : 'Grade' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-vl-ink-muted py-6 text-center">No student submissions yet.</p>
                        @endforelse
                    </div>
                </x-card>
            @endif

            @if(!$isInstructor && $submission)
                <x-card title="My Submission" :padding="true">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <x-badge :variant="$submission->status === 'graded' ? 'success' : 'info'">{{ ucfirst($submission->status) }}</x-badge>
                        @if($submission->status === 'graded')
                            <span class="font-mono text-lg font-bold text-vl-teal tabular-nums">{{ $submission->grade }} / {{ $assignment->max_points }}</span>
                        @endif
                    </div>

                    @if($submission->feedback)
                        <div class="rounded-xl bg-vl-surface-raised/75 p-4 space-y-1">
                            <p class="text-xs font-semibold text-vl-ink">Instructor Feedback</p>
                            <p class="text-sm leading-relaxed text-vl-ink-secondary whitespace-pre-wrap">{{ $submission->feedback }}</p>
                        </div>
                    @endif
                </x-card>
            @endif
        </div>

        <div class="lg:col-span-4 space-y-5">
            @if(!$isInstructor)
                <x-card title="Your Lab Status" :padding="true">
                    @if(!$submission || $submission->status === 'not_started')
                        <form method="POST" action="{{ route('submissions.start', $assignment->id) }}">
                            @csrf
                            <button type="submit" class="vl-btn-primary w-full">Start Assignment</button>
                        </form>
                    @elseif($submission->status === 'in_progress')
                        <div class="space-y-3">
                            <a href="{{ route('submissions.ide', $assignment->id) }}" class="vl-btn-primary w-full">Open in IDE</a>
                            <form method="POST" action="{{ route('submissions.submit', $assignment->id) }}">
                                @csrf
                                <button type="button" onclick="vcConfirm('Submit your current workspace snapshot for grading?', () => this.form.submit())" class="vl-btn-secondary w-full !text-vl-teal">
                                    Submit Assignment
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <x-badge :variant="$submission->status === 'graded' ? 'success' : 'info'" class="!px-3.5 !py-1.5 !text-xs">
                                {{ ucfirst($submission->status) }}
                            </x-badge>
                        </div>
                    @endif
                </x-card>
            @endif

            <x-card title="Specifications" :padding="true">
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-vl-ink-secondary">Max Points</dt>
                        <dd class="font-mono font-bold text-vl-ink tabular-nums">{{ $assignment->max_points }}</dd>
                    </div>
                    @if($assignment->due_date)
                        <div class="flex items-center justify-between">
                            <dt class="text-vl-ink-secondary">Due Date</dt>
                            <dd class="font-mono text-xs font-semibold {{ $assignment->isOverdue() ? 'text-vl-danger' : 'text-vl-ink' }}">
                                {{ $assignment->due_date->format('M d, Y') }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <dt class="text-vl-ink-secondary">Runtime</dt>
                        <dd class="font-medium text-vl-ink">{{ ucfirst($assignment->starter_language) }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>
</div>
@endsection
