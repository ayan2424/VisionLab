@extends('layouts.dashboard')

@section('title', 'My Progress')
@section('page-title', 'My Progress')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="vc-card p-6 border-l-4" style="border-left-color:var(--vc-accent);">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color:var(--vc-text);">My Progress</h1>
                <p class="text-sm mt-1" style="color:var(--vc-muted);">All assignments and grades across your enrolled courses</p>
            </div>
            {{-- Overall GPA ring --}}
            @if($overallPct !== null)
            <div class="flex items-center gap-5">
                <div class="text-right">
                    <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--vc-muted);">Overall Average</div>
                    <div class="text-3xl font-black" style="color:{{ $overallPct >= 80 ? 'var(--vc-success)' : ($overallPct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)') }};">
                        {{ number_format($overallPct, 0) }}%
                    </div>
                </div>
                <div class="relative w-16 h-16">
                    @php
                        $c = 2 * pi() * 26;
                        $d = ($overallPct / 100) * $c;
                        $col = $overallPct >= 80 ? 'var(--vc-success)' : ($overallPct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)');
                    @endphp
                    <svg class="w-16 h-16" viewBox="0 0 64 64" style="transform:rotate(-90deg)">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="var(--vc-border)" stroke-width="6"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="{{ $col }}" stroke-width="6"
                                stroke-dasharray="{{ number_format($d,2) }} {{ number_format($c,2) }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-black" style="color:{{ $col }};">{{ $gradeLetter }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Summary stat row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Courses',      'value' => $stats['courses'],      'color' => 'var(--vc-accent)'],
            ['label' => 'Completed',    'value' => $stats['graded'],       'color' => 'var(--vc-success)'],
            ['label' => 'In Progress',  'value' => $stats['in_progress'],  'color' => 'var(--vc-warning)'],
            ['label' => 'Pending',      'value' => $stats['pending'],      'color' => 'var(--vc-info)'],
        ] as $i => $s)
        <div class="vc-card p-5 text-center animate-float" style="animation:fadeSlideUp .3s ease both;animation-delay:{{ $i*50 }}ms;">
            <div class="text-3xl font-black mb-1" style="color:{{ $s['color'] }}">{{ $s['value'] }}</div>
            <div class="text-xs font-semibold uppercase tracking-wider" style="color:var(--vc-text-secondary);">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Contribution Heatmap --}}
    <div class="vc-card p-6" style="opacity:0;animation:fadeSlideUp .3s .2s ease forwards;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold flex items-center gap-2" style="color:var(--vc-text);">
                <svg class="w-4 h-4" style="color:var(--vc-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                Activity Heatmap
            </h3>
            <div class="flex items-center gap-4">
                <div id="heatmap-streak" class="text-xs font-semibold" style="color:var(--vc-muted);"></div>
                <div id="heatmap-total" class="text-xs font-semibold" style="color:var(--vc-muted);"></div>
            </div>
        </div>

        <div id="contribution-heatmap" class="overflow-x-auto pb-2" style="min-height:100px;">
            <div class="text-center py-8 text-xs" style="color:var(--vc-muted);">Loading activity data...</div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center justify-end gap-1 mt-3">
            <span class="text-[10px]" style="color:var(--vc-muted);">Less</span>
            @foreach(['var(--vc-elevated)', 'rgba(240,80,0,0.2)', 'rgba(240,80,0,0.4)', 'rgba(240,80,0,0.6)', 'rgba(240,80,0,0.85)'] as $c)
            <div class="w-3 h-3 rounded-sm" style="background:{{ $c }};"></div>
            @endforeach
            <span class="text-[10px]" style="color:var(--vc-muted);">More</span>
        </div>
    </div>

    <script>
    (async () => {
        try {
            const res = await fetch('{{ route("contributions.heatmap") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            // Update stats
            const streakEl = document.getElementById('heatmap-streak');
            const totalEl = document.getElementById('heatmap-total');
            if (streakEl) streakEl.textContent = `🔥 ${data.current_streak}d streak`;
            if (totalEl) totalEl.textContent = `${data.total_contributions} contributions`;

            // Build heatmap
            const container = document.getElementById('contribution-heatmap');
            if (!container || !data.heatmap) return;

            const weeks = [];
            let currentWeek = [];
            data.heatmap.forEach((day, i) => {
                const d = new Date(day.date);
                if (i === 0) {
                    // Pad the first week
                    for (let p = 0; p < d.getDay(); p++) currentWeek.push(null);
                }
                currentWeek.push(day);
                if (d.getDay() === 6 || i === data.heatmap.length - 1) {
                    weeks.push(currentWeek);
                    currentWeek = [];
                }
            });

            const colors = ['var(--vc-elevated)', 'rgba(240,80,0,0.2)', 'rgba(240,80,0,0.4)', 'rgba(240,80,0,0.6)', 'rgba(240,80,0,0.85)'];
            const cellSize = 12;
            const gap = 2;
            const totalW = weeks.length * (cellSize + gap);

            let svg = `<svg width="${totalW}" height="${7 * (cellSize + gap)}" style="display:block;">`;
            weeks.forEach((week, wi) => {
                week.forEach((day, di) => {
                    if (!day) return;
                    const x = wi * (cellSize + gap);
                    const y = di * (cellSize + gap);
                    const color = colors[day.level] || colors[0];
                    const title = `${day.date}: ${day.count} contributions`;
                    svg += `<rect x="${x}" y="${y}" width="${cellSize}" height="${cellSize}" rx="2" fill="${color}" style="cursor:pointer;"><title>${title}</title></rect>`;
                });
            });
            svg += '</svg>';
            container.innerHTML = svg;
        } catch (e) {
            const c = document.getElementById('contribution-heatmap');
            if (c) c.innerHTML = '<div class="text-center py-4 text-xs" style="color:var(--vc-muted);">Unable to load heatmap.</div>';
        }
    })();
    </script>

    @forelse($courseProgress as $cp)
    @php
        $course    = $cp['course'];
        $gradedPct = $cp['graded_pct'];
        $cpColor   = $gradedPct >= 80 ? 'var(--vc-success)' : ($gradedPct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)');
    @endphp

    {{-- Course card --}}
    <div class="vc-card p-6 space-y-5 animate-float" style="animation:fadeSlideUp .3s ease both;animation-delay:100ms;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg font-black text-white"
                     style="background:var(--vc-accent);">
                    {{ strtoupper(substr($course->title, 0, 1)) }}
                </div>
                <div>
                    <a href="{{ route('courses.show', $course->slug) }}" class="text-lg font-bold transition-colors hover:text-current" style="color:var(--vc-text);" onmouseover="this.style.color='var(--vc-accent)';" onmouseout="this.style.color='var(--vc-text)';">
                        {{ $course->title }}
                    </a>
                    <div class="text-sm font-medium mt-0.5" style="color:var(--vc-text-secondary);">{{ $course->instructor->name }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-semibold mb-1" style="color:var(--vc-muted);">{{ $cp['graded_count'] }} / {{ $cp['total_count'] }} graded</div>
                @if($cp['average'] !== null)
                <div class="text-xl font-black" style="color:{{ $cpColor }};">
                    {{ number_format($cp['average'], 0) }}% avg
                </div>
                @endif
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="h-2 rounded-full overflow-hidden" style="background:var(--vc-elevated);">
            <div class="h-full rounded-full transition-all duration-700" style="width:{{ $cp['completion_pct'] }}%;background:var(--vc-accent);"></div>
        </div>

        {{-- Assignments list --}}
        <div class="space-y-2">
            @foreach($cp['assignments'] as $row)
            @php
                $a   = $row['assignment'];
                $sub = $row['submission'];
                $pct = $row['pct'];
            @endphp
            <div class="flex items-center justify-between rounded-xl px-4 py-3 transition-colors" style="background:var(--vc-bg);border:1px solid var(--vc-border);" onmouseover="this.style.borderColor='var(--vc-accent)';" onmouseout="this.style.borderColor='var(--vc-border)';">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Status dot --}}
                    @if($sub && $sub->status === 'graded')
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $pct >= 80 ? 'var(--vc-success)' : ($pct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)') }};"></div>
                    @elseif($sub && $sub->status === 'submitted')
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:var(--vc-info);"></div>
                    @elseif($sub && $sub->status === 'in_progress')
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 animate-pulse" style="background:var(--vc-warning);"></div>
                    @elseif($sub && $sub->status === 'late')
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:var(--vc-danger);"></div>
                    @else
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:var(--vc-muted);"></div>
                    @endif

                    <div class="min-w-0">
                        <a href="{{ route('assignments.show', $a->id) }}" class="text-sm font-bold transition-colors line-clamp-1" style="color:var(--vc-text);" onmouseover="this.style.color='var(--vc-accent)';" onmouseout="this.style.color='var(--vc-text)';">
                            {{ $a->title }}
                        </a>
                        <div class="text-xs font-medium mt-0.5" style="color:var(--vc-text-secondary);">
                            {{ $a->max_points }} pts
                            @if($a->due_date)
                            · Due {{ $a->due_date->format('M d') }}
                            @if($a->isOverdue() && (!$sub || !in_array($sub->status, ['submitted','graded'])))
                            <span class="font-bold ml-1" style="color:var(--vc-danger);">· Overdue</span>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 ml-4 flex-shrink-0">
                    @if($sub && $sub->status === 'graded')
                        {{-- Grade bar --}}
                        <div class="hidden sm:flex items-center gap-2">
                            <div class="w-20 h-1.5 rounded-full overflow-hidden" style="background:var(--vc-elevated);">
                                <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $pct >= 80 ? 'var(--vc-success)' : ($pct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)') }};"></div>
                            </div>
                        </div>
                        <span class="text-sm font-black" style="color:{{ $pct >= 80 ? 'var(--vc-success)' : ($pct >= 60 ? 'var(--vc-warning)' : 'var(--vc-danger)') }};">
                            {{ $sub->grade }}/{{ $a->max_points }}
                        </span>
                        @if($sub->feedback)
                        <a href="{{ route('submissions.show', $sub->id) }}" class="btn-secondary text-xs py-1 px-3">View</a>
                        @endif
                    @elseif($sub && $sub->status === 'submitted')
                        <span class="badge text-xs" style="color:var(--vc-info);background:rgba(59,130,246,0.1);border-color:rgba(59,130,246,0.2);">Submitted</span>
                    @elseif($sub && $sub->status === 'late')
                        <span class="badge text-xs" style="color:var(--vc-danger);background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2);">Late</span>
                    @elseif($sub && $sub->status === 'in_progress')
                        <a href="{{ route('submissions.ide', $a->id) }}" class="btn-secondary text-xs py-1.5 px-4" style="color:var(--vc-accent);border-color:var(--vc-accent);">Continue</a>
                    @else
                        <form method="POST" action="{{ route('submissions.start', $a->id) }}">
                            @csrf
                            <button type="submit" class="btn-primary text-xs py-1.5 px-4">Start</button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="vc-card py-24 text-center">
        <svg class="w-12 h-12 mx-auto mb-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <h3 class="text-base font-bold mb-2" style="color:var(--vc-text);">No courses enrolled</h3>
        <p class="text-sm mb-6" style="color:var(--vc-text-secondary);">Join a course to start tracking your progress.</p>
        <a href="{{ route('enrollments.join') }}" class="btn-primary">Join a Course</a>
    </div>
    @endforelse
</div>
@endsection


