@extends('layouts.dashboard')
@section('title', 'My Progress')
@section('page-title', 'My Progress')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header with Overall Grade Ring --}}
    <div class="vl-panel p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">My Progress</h2>
            <p class="text-sm text-vl-ink-secondary">Assignments, completion pace, and verified grades across enrolled courses.</p>
        </div>

        @if($overallPct !== null)
            @php
                $c = 2 * pi() * 26;
                $d = ($overallPct / 100) * $c;
                $ringColor = $overallPct >= 80 ? 'rgb(var(--vl-teal))' : ($overallPct >= 60 ? 'rgb(var(--vl-gate))' : 'rgb(var(--vl-danger))');
            @endphp
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-right">
                    <p class="text-xs font-medium text-vl-ink-muted">Overall Average</p>
                    <p class="font-mono text-2xl font-bold tabular-nums" style="color: {{ $ringColor }}">{{ number_format($overallPct, 0) }}%</p>
                </div>
                <div class="relative h-16 w-16">
                    <svg class="h-16 w-16 -rotate-90" viewBox="0 0 64 64" aria-hidden="true">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="rgb(var(--vl-border) / 0.12)" stroke-width="6" />
                        <circle cx="32" cy="32" r="26" fill="none" stroke="{{ $ringColor }}" stroke-width="6" stroke-dasharray="{{ number_format($d, 2) }} {{ number_format($c, 2) }}" stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="font-mono text-base font-bold" style="color: {{ $ringColor }}">{{ $gradeLetter }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tabular KPI Strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-stat-card label="Courses" :value="$stats['courses']" tone="accent" />
        <x-stat-card label="Completed" :value="$stats['graded']" tone="success" />
        <x-stat-card label="In Progress" :value="$stats['in_progress']" tone="warning" />
        <x-stat-card label="Pending" :value="$stats['pending']" tone="info" />
    </div>

    {{-- Theme-Reactive Contribution Heatmap --}}
    <x-card title="Activity Heatmap" :padding="true">
        <x-slot:actions>
            <div class="flex items-center gap-4 font-mono text-xs text-vl-ink-muted tabular-nums">
                <span id="heatmap-streak"></span>
                <span id="heatmap-total"></span>
            </div>
        </x-slot:actions>

        <div id="contribution-heatmap" class="overflow-x-auto pb-2 min-h-[104px]">
            <div class="text-center py-8 text-xs text-vl-ink-muted">Loading contribution activity...</div>
        </div>

        <div class="mt-3 flex items-center justify-end gap-1.5 text-[11px] text-vl-ink-muted">
            <span>Less</span>
            <span class="h-3 w-3 rounded-[3px] bg-vl-surface-raised"></span>
            <span class="h-3 w-3 rounded-[3px] bg-vl-teal/25"></span>
            <span class="h-3 w-3 rounded-[3px] bg-vl-teal/50"></span>
            <span class="h-3 w-3 rounded-[3px] bg-vl-teal/75"></span>
            <span class="h-3 w-3 rounded-[3px] bg-vl-teal"></span>
            <span>More</span>
        </div>
    </x-card>

    {{-- Per-Course Breakdown --}}
    @forelse($courseProgress as $cp)
        @php
            $course = $cp['course'];
            $gradedPct = $cp['graded_pct'];
            $avgTone = $gradedPct >= 80 ? 'text-vl-teal' : ($gradedPct >= 60 ? 'text-vl-gate' : 'text-vl-danger');
        @endphp
        <div class="vl-panel p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="h-11 w-11 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr($course->title, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <a href="{{ route('courses.show', $course->slug) }}" class="text-base font-bold text-vl-ink hover:text-vl-accent transition-colors truncate block">
                            {{ $course->title }}
                        </a>
                        <p class="text-xs text-vl-ink-secondary">{{ $course->instructor->name }}</p>
                    </div>
                </div>

                <div class="sm:text-right tabular-nums shrink-0">
                    <p class="text-xs text-vl-ink-muted">{{ $cp['graded_count'] }} / {{ $cp['total_count'] }} graded</p>
                    @if($cp['average'] !== null)
                        <p class="font-mono text-lg font-bold {{ $avgTone }}">{{ number_format($cp['average'], 0) }}% avg</p>
                    @endif
                </div>
            </div>

            <div class="h-2 rounded-full bg-vl-surface-raised overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-vl-accent to-vl-teal" style="width: {{ min(100, max(0, (float) $cp['completion_pct'])) }}%"></div>
            </div>

            <div class="space-y-2">
                @foreach($cp['assignments'] as $row)
                    @php
                        $a = $row['assignment'];
                        $sub = $row['submission'];
                        $pct = $row['pct'];
                        $scoreTone = $pct >= 80 ? 'text-vl-teal' : ($pct >= 60 ? 'text-vl-gate' : 'text-vl-danger');
                        $barTone = $pct >= 80 ? 'bg-vl-teal' : ($pct >= 60 ? 'bg-vl-gate' : 'bg-vl-danger');
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl px-4 py-3 bg-vl-surface-raised/65 hover:bg-vl-surface-raised transition-colors duration-150">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="h-2 w-2 rounded-full shrink-0 {{ ($sub && $sub->status === 'graded') ? $barTone : (($sub && $sub->status === 'submitted') ? 'bg-vl-accent' : (($sub && $sub->status === 'late') ? 'bg-vl-danger' : (($sub && $sub->status === 'in_progress') ? 'bg-vl-gate' : 'bg-vl-border/30'))) }}"></span>
                            <div class="min-w-0">
                                <a href="{{ route('assignments.show', $a->id) }}" class="text-sm font-semibold text-vl-ink hover:text-vl-accent transition-colors block truncate">
                                    {{ $a->title }}
                                </a>
                                <p class="text-xs text-vl-ink-muted tabular-nums">
                                    {{ $a->max_points }} pts
                                    @if($a->due_date)
                                        &middot; Due {{ $a->due_date->format('M d') }}
                                        @if($a->isOverdue() && (!$sub || !in_array($sub->status, ['submitted', 'graded'])))
                                            <span class="font-semibold text-vl-danger">&middot; Overdue</span>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($sub && $sub->status === 'graded')
                                <div class="hidden sm:block w-20 h-1.5 rounded-full bg-vl-surface overflow-hidden">
                                    <div class="h-full rounded-full {{ $barTone }}" style="width: {{ min(100, max(0, (float) $pct)) }}%"></div>
                                </div>
                                <span class="font-mono text-sm font-bold tabular-nums {{ $scoreTone }}">{{ $sub->grade }}/{{ $a->max_points }}</span>
                                @if($sub->feedback)
                                    <a href="{{ route('submissions.show', $sub->id) }}" class="vl-btn-secondary !py-1 !px-3 !text-xs">View</a>
                                @endif
                            @elseif($sub && $sub->status === 'submitted')
                                <x-badge variant="info">Submitted</x-badge>
                            @elseif($sub && $sub->status === 'late')
                                <x-badge variant="danger">Late</x-badge>
                            @elseif($sub && $sub->status === 'in_progress')
                                <a href="{{ route('submissions.ide', $a->id) }}" class="vl-btn-secondary !py-1.5 !px-3.5 !text-xs">Continue</a>
                            @else
                                <form method="POST" action="{{ route('submissions.start', $a->id) }}">
                                    @csrf
                                    <button type="submit" class="vl-btn-primary !py-1.5 !px-3.5 !text-xs">Start</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <x-card :padding="true">
            <x-empty-state title="No courses enrolled yet" description="Join a course to start tracking your lab progress and grades.">
                <a href="{{ route('enrollments.join') }}" class="vl-btn-primary !py-2 !px-5 !text-xs">Join a Course</a>
            </x-empty-state>
        </x-card>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
(() => {
    let cachedHeatmap = null;

    function getHeatmapColors() {
        const styles = getComputedStyle(document.documentElement);
        const raised = styles.getPropertyValue('--vl-surface-raised').trim();
        const teal = styles.getPropertyValue('--vl-teal').trim();
        return [
            `rgb(${raised})`,
            `rgb(${teal} / 0.28)`,
            `rgb(${teal} / 0.52)`,
            `rgb(${teal} / 0.76)`,
            `rgb(${teal})`,
        ];
    }

    function renderHeatmap(data) {
        const container = document.getElementById('contribution-heatmap');
        if (!container || !data?.heatmap) return;

        const weeks = [];
        let currentWeek = [];
        data.heatmap.forEach((day, i) => {
            const d = new Date(day.date);
            if (i === 0) {
                for (let p = 0; p < d.getDay(); p++) currentWeek.push(null);
            }
            currentWeek.push(day);
            if (d.getDay() === 6 || i === data.heatmap.length - 1) {
                weeks.push(currentWeek);
                currentWeek = [];
            }
        });

        const colors = getHeatmapColors();
        const cellSize = 12;
        const gap = 3;
        const totalW = weeks.length * (cellSize + gap);

        let svg = `<svg width="${totalW}" height="${7 * (cellSize + gap)}" class="block">`;
        weeks.forEach((week, wi) => {
            week.forEach((day, di) => {
                if (!day) return;
                const x = wi * (cellSize + gap);
                const y = di * (cellSize + gap);
                const color = colors[Math.min(4, Math.max(0, day.level || 0))];
                const title = `${day.date}: ${day.count} contributions`;
                svg += `<rect x="${x}" y="${y}" width="${cellSize}" height="${cellSize}" rx="3" fill="${color}"><title>${title}</title></rect>`;
            });
        });
        svg += '</svg>';
        container.innerHTML = svg;
    }

    window.addEventListener('vl:theme-changed', () => {
        if (cachedHeatmap) renderHeatmap(cachedHeatmap);
    });

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await fetch('{{ route("contributions.heatmap") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            cachedHeatmap = data;

            const streakEl = document.getElementById('heatmap-streak');
            const totalEl = document.getElementById('heatmap-total');
            if (streakEl) streakEl.textContent = `${data.current_streak}d streak`;
            if (totalEl) totalEl.textContent = `${data.total_contributions} contributions`;

            renderHeatmap(data);
        } catch {
            const c = document.getElementById('contribution-heatmap');
            if (c) c.innerHTML = '<div class="text-center py-6 text-xs text-vl-ink-muted">Unable to load activity data.</div>';
        }
    });
})();
</script>
@endpush
