@extends('layouts.dashboard')

@section('title', 'Grading Queue')
@section('page-title', 'Grading Queue')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="vc-card p-6 border-l-4" style="border-left-color:var(--vc-accent);">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Grading Queue</h1>
                <p class="text-sm mt-1" style="color:var(--vc-muted);">
                    @if($pendingCount > 0)
                    <span class="font-semibold" style="color:var(--vc-warning);">{{ $pendingCount }}</span> submission{{ $pendingCount !== 1 ? 's' : '' }} waiting to be graded
                    @else
                    All caught up — no pending submissions
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($pendingCount > 0)
                <div class="w-10 h-10 rounded-full flex items-center justify-center border" style="background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.2);">
                    <span class="text-sm font-black" style="color:var(--vc-warning);">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                </div>
                @else
                <div class="w-10 h-10 rounded-full flex items-center justify-center border" style="background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.2);">
                    <svg class="w-5 h-5" style="color:var(--vc-success);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'To Grade',   'value' => $stats['pending'],  'color' => 'var(--vc-warning)'],
            ['label' => 'Late',        'value' => $stats['late'],     'color' => 'var(--vc-danger)'],
            ['label' => 'Graded',      'value' => $stats['graded'],   'color' => 'var(--vc-success)'],
            ['label' => 'Total',       'value' => $stats['total'],    'color' => 'var(--vc-info)'],
        ] as $i => $s)
        <div class="vc-card p-4 text-center animate-float" style="animation:fadeSlideUp .3s ease both;animation-delay:{{ $i*50 }}ms;">
            <div class="text-3xl font-black mb-1" style="color:{{ $s['color'] }}">{{ $s['value'] }}</div>
            <div class="text-xs font-medium" style="color:var(--vc-text-secondary);">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Course filter tabs --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('submissions.queue') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all border {{ !$courseFilter ? 'border-transparent text-white' : 'border-transparent text-current hover:border-current' }}"
           style="{{ !$courseFilter ? 'background:var(--vc-accent);color:white;' : 'background:transparent;color:var(--vc-text-secondary);border-color:var(--vc-border);' }}">
            All Courses
        </a>
        @foreach($courses as $c)
        <a href="{{ route('submissions.queue', ['course' => $c->id]) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border {{ $courseFilter == $c->id ? 'border-transparent text-white' : 'border-transparent text-current hover:border-current' }}"
           style="{{ $courseFilter == $c->id ? 'background:var(--vc-accent);color:white;' : 'background:transparent;color:var(--vc-text-secondary);border-color:var(--vc-border);' }}">
            {{ $c->title }}
            @if(isset($perCoursePending[$c->id]) && $perCoursePending[$c->id] > 0)
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold" style="background:rgba(255,255,255,0.2);color:inherit;">{{ $perCoursePending[$c->id] }}</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Status filter --}}
    <div class="flex items-center gap-2">
        <span class="text-sm font-medium" style="color:var(--vc-muted);">Show:</span>
        @foreach(['all' => 'All', 'pending' => 'Pending', 'graded' => 'Graded'] as $f => $label)
        <a href="{{ route('submissions.queue', array_merge(request()->only('course'), ['status' => $f])) }}"
           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-all"
           style="{{ $statusFilter === $f ? 'background:var(--vc-elevated);color:var(--vc-text);' : 'color:var(--vc-text-secondary);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($submissions->isEmpty())
    <div class="vc-card py-20 text-center">
        <svg class="w-12 h-12 mx-auto mb-3" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <p class="text-sm font-medium" style="color:var(--vc-text-secondary);">{{ $statusFilter === 'pending' ? 'No pending submissions — all caught up!' : 'No submissions found.' }}</p>
    </div>
    @else

    {{-- Group by assignment --}}
    @foreach($grouped as $assignmentId => $group)
    @php $assignment = $group['assignment']; $subs = $group['submissions']; @endphp
    <div class="space-y-3">
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('assignments.show', $assignmentId) }}" class="text-base font-bold transition-colors hover:text-current" style="color:var(--vc-text);text-decoration:none;" onmouseover="this.style.color='var(--vc-accent)';" onmouseout="this.style.color='var(--vc-text)';">
                    {{ $assignment->title }}
                </a>
                <span class="text-sm" style="color:var(--vc-muted);">·</span>
                <span class="text-sm font-medium" style="color:var(--vc-text-secondary);">{{ $assignment->course->title }}</span>
                @if($assignment->due_date)
                <span class="text-sm" style="color:var(--vc-muted);">·</span>
                <span class="text-sm font-medium" style="{{ $assignment->isOverdue() ? 'color:var(--vc-danger);' : 'color:var(--vc-text-secondary);' }}">Due {{ $assignment->due_date->format('M d') }}</span>
                @endif
            </div>
            <span class="text-xs font-semibold" style="color:var(--vc-muted);">{{ $subs->count() }} submission{{ $subs->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="vc-card overflow-hidden">
            @foreach($subs as $sub)
            <div class="flex items-center justify-between px-5 py-4 border-b last:border-0 transition-colors" style="border-color:var(--vc-border);" onmouseover="this.style.backgroundColor='var(--vc-elevated)';" onmouseout="this.style.backgroundColor='transparent';">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:var(--vc-info);">
                        {{ strtoupper(substr($sub->student->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold" style="color:var(--vc-text);">{{ $sub->student->name }}</div>
                        <div class="text-xs mt-0.5" style="color:var(--vc-text-secondary);">
                            {{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : 'Not submitted' }}
                            @if($sub->isLate()) <span class="font-bold" style="color:var(--vc-danger);">&middot; Late</span> @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if($sub->status === 'graded')
                    @php $pct = $assignment->max_points > 0 ? $sub->grade / $assignment->max_points * 100 : 0; @endphp
                    <span class="text-base font-black" style="color:{{ $pct >= 80 ? 'var(--vc-success)' : ($pct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)') }};">
                        {{ $sub->grade }}/{{ $assignment->max_points }}
                    </span>
                    @endif
                    <span class="badge {{ $sub->status_badge_class }}">{{ ucfirst($sub->status) }}</span>
                    @if(in_array($sub->status, ['submitted', 'late', 'graded']))
                    <a href="{{ route('submissions.show', $sub->id) }}" class="{{ $sub->status === 'graded' ? 'btn-secondary' : 'btn-primary' }} text-xs py-1.5 px-4">
                        {{ $sub->status === 'graded' ? 'Review' : 'Grade' }}
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="mt-6">{{ $submissions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
