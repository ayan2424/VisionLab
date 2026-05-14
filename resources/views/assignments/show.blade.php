@extends('layouts.dashboard')

@section('title', $assignment->title)
@section('page-title', 'Assignment Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="vc-card p-6 border-l-4" style="border-left-color:var(--vc-accent);">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs mb-2" style="color:var(--vc-text-secondary);">
                    <a href="{{ route('courses.index') }}" class="hover:text-current transition-colors">Courses</a>
                    <span>/</span>
                    <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-current transition-colors">{{ $course->title }}</a>
                    <span>/</span>
                    <span style="color:var(--vc-muted);">{{ $assignment->title }}</span>
                </div>
                <h1 class="text-2xl font-bold" style="color:var(--vc-text);">{{ $assignment->title }}</h1>
                <div class="flex items-center gap-4 mt-2 text-sm" style="color:var(--vc-text-secondary);">
                    @if($assignment->due_date)
                    <span class="{{ $assignment->isOverdue() ? 'font-bold' : '' }}" style="{{ $assignment->isOverdue() ? 'color:var(--vc-danger);' : '' }}">
                        Due: {{ $assignment->due_date->format('M d, Y H:i') }}
                        @if(!$assignment->isOverdue()) ({{ $assignment->time_remaining }}) @endif
                    </span>
                    @endif
                    <span>{{ $assignment->max_points }} points</span>
                    <span class="badge">{{ ucfirst($assignment->starter_language) }}</span>
                </div>
            </div>
            @if($isInstructor)
            <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn-secondary text-xs py-2 px-4">Edit</a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-xl text-sm" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:var(--vc-success);">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            @if($assignment->description)
            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-3" style="color:var(--vc-text);">Instructions</h3>
                <div class="text-sm whitespace-pre-wrap" style="color:var(--vc-text-secondary);">{{ $assignment->description }}</div>
            </div>
            @endif

            {{-- Starter Code --}}
            @if($assignment->starter_code)
            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-3" style="color:var(--vc-text);">Starter Code</h3>
                <pre class="text-xs overflow-x-auto rounded-xl p-4" style="background:var(--vc-bg);color:var(--vc-text);font-family:'JetBrains Mono',monospace;border:1px solid var(--vc-border);">{{ $assignment->starter_code }}</pre>
            </div>
            @endif

            {{-- Instructor: submissions table --}}
            @if($isInstructor)
            <div class="vc-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold" style="color:var(--vc-text);">Submissions ({{ $submissions->count() }})</h3>
                    @php $pending = $submissions->whereIn('status', ['submitted','late'])->count(); @endphp
                    @if($pending > 0)
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold" style="background:rgba(245,158,11,0.1);color:var(--vc-warning);border:1px solid rgba(245,158,11,0.2);">{{ $pending }} to grade</span>
                    @endif
                </div>
                @forelse($submissions as $sub)
                <div class="flex items-center justify-between py-3 border-b last:border-0" style="border-color:var(--vc-border);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background:var(--vc-accent);">
                            {{ strtoupper(substr($sub->student->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm" style="color:var(--vc-text);">{{ $sub->student->name }}</div>
                            <div class="text-xs" style="color:var(--vc-muted);">
                                {{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : 'Not submitted' }}
                                @if($sub->isLate()) <span style="color:var(--vc-danger);">&middot; Late</span> @endif
                                @if($sub->status === 'graded') <span style="color:var(--vc-success);">&middot; {{ $sub->grade }}/{{ $assignment->max_points }} pts</span> @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold border {{ $sub->status_badge_class }}">{{ ucfirst($sub->status) }}</span>
                        @if(in_array($sub->status, ['submitted','late','graded']))
                        <a href="{{ route('submissions.show', $sub->id) }}" class="btn-secondary text-xs py-1.5 px-3">
                            {{ $sub->status === 'graded' ? 'Review' : 'Grade' }}
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-sm" style="color:var(--vc-muted);">No submissions yet.</div>
                @endforelse
            </div>
            @endif

            {{-- Student: my submission --}}
            @if(!$isInstructor && $submission)
            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-4" style="color:var(--vc-text);">My Submission</h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold border {{ $submission->status_badge_class }}">{{ ucfirst($submission->status) }}</span>
                    @if($submission->status === 'graded')
                    <span class="text-lg font-bold" style="color:var(--vc-success);">{{ $submission->grade }}/{{ $assignment->max_points }}</span>
                    @endif
                </div>
                @if($submission->feedback)
                <div class="px-4 py-3 rounded-xl" style="background:var(--vc-elevated);border:1px solid var(--vc-border);">
                    <div class="text-xs font-semibold mb-1" style="color:var(--vc-muted);">Feedback</div>
                    <div class="text-sm" style="color:var(--vc-text-secondary);">{{ $submission->feedback }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            @if(!$isInstructor)
            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-4" style="color:var(--vc-text);">Your Status</h3>
                @if(!$submission || $submission->status === 'not_started')
                <form method="POST" action="{{ route('submissions.start', $assignment->id) }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full justify-center">Start Assignment</button>
                </form>
                @elseif($submission->status === 'in_progress')
                <a href="{{ route('submissions.ide', $assignment->id) }}" class="btn-primary w-full justify-center mb-3">Open in IDE</a>
                <form method="POST" action="{{ route('submissions.submit', $assignment->id) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Submit this assignment?')" class="w-full py-2.5 rounded-xl text-sm font-bold text-white transition-all" style="background:var(--vc-success);">
                        Submit Assignment
                    </button>
                </form>
                @else
                <div class="text-center py-2">
                    <span class="px-4 py-2 rounded-xl text-sm font-semibold border {{ $submission->status_badge_class }}">{{ ucfirst($submission->status) }}</span>
                </div>
                @endif
            </div>
            @endif

            <div class="vc-card p-5">
                <h3 class="text-sm font-bold mb-3" style="color:var(--vc-text);">Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between" style="color:var(--vc-text-secondary);">
                        <span>Points</span><span class="font-bold" style="color:var(--vc-text);">{{ $assignment->max_points }}</span>
                    </div>
                    @if($assignment->due_date)
                    <div class="flex justify-between" style="color:var(--vc-text-secondary);">
                        <span>Due</span><span class="{{ $assignment->isOverdue() ? 'font-bold' : '' }}" style="{{ $assignment->isOverdue() ? 'color:var(--vc-danger);' : 'color:var(--vc-text);' }}">{{ $assignment->due_date->format('M d') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between" style="color:var(--vc-text-secondary);">
                        <span>Language</span><span style="color:var(--vc-text);">{{ ucfirst($assignment->starter_language) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
