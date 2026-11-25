@extends('layouts.landing')

@section('title', $user->name . ' — VisionLab Student Portfolio')
@section('meta_description', 'Verified coding portfolio, lab activity heatmap, runtime stack, and academic badges for ' . $user->name . ' on VisionLab.')

@section('content')
    <section class="vl-atmosphere pt-12 pb-20 lg:pt-16 lg:pb-24">
        <div class="mx-auto max-w-6xl px-5 sm:px-8 space-y-8">
            {{-- Top Institutional Profile Banner --}}
            <div class="vl-panel p-7 sm:p-9 relative overflow-hidden">
                <div aria-hidden="true" class="pointer-events-none absolute -top-24 right-0 h-64 w-64 rounded-full bg-vl-accent/12 blur-[80px]"></div>

                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        @if($user->avatar_url)
                            <img
                                src="{{ $user->avatar_url }}"
                                alt="{{ $user->name }}"
                                class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl object-cover shadow-vl-panel"
                            >
                        @else
                            <div class="h-20 w-20 sm:h-24 sm:w-24 rounded-2xl bg-gradient-to-br from-vl-accent to-vl-teal text-white font-bold text-2xl sm:text-3xl flex items-center justify-center shadow-vl-panel">
                                {{ $user->avatar_initials }}
                            </div>
                        @endif

                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-vl-ink">{{ $user->name }}</h1>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-vl-accent/12 px-3 py-0.5 font-mono text-xs font-semibold text-vl-accent tabular-nums">
                                    Level {{ $user->level ?? 1 }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-vl-ink-secondary">
                                <span class="font-mono text-xs text-vl-ink-muted">{{ $user->student_id }}</span>
                                <span aria-hidden="true" class="text-vl-border/30">&middot;</span>
                                <span>{{ $user->rank_title }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Key Verified Stats --}}
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 w-full md:w-auto">
                        <div class="rounded-2xl bg-vl-surface-raised/85 px-4 py-3 text-center">
                            <p class="font-mono text-lg sm:text-xl font-bold text-vl-ink tabular-nums">{{ number_format($user->xp ?? 0) }}</p>
                            <p class="text-[11px] font-medium text-vl-ink-muted mt-0.5">Total XP</p>
                        </div>
                        <div class="rounded-2xl bg-vl-surface-raised/85 px-4 py-3 text-center">
                            <p class="font-mono text-lg sm:text-xl font-bold text-vl-ink tabular-nums">{{ number_format($totalWorkspaces) }}</p>
                            <p class="text-[11px] font-medium text-vl-ink-muted mt-0.5">Workspaces</p>
                        </div>
                        <div class="rounded-2xl bg-vl-surface-raised/85 px-4 py-3 text-center">
                            <p class="font-mono text-lg sm:text-xl font-bold text-vl-teal tabular-nums inline-flex items-center justify-center gap-1">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                </svg>
                                {{ $user->longest_streak ?? 0 }}d
                            </p>
                            <p class="text-[11px] font-medium text-vl-ink-muted mt-0.5">Best Streak</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Middle Grid: Runtime Stack & Coding Rhythm --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Verified Tech Stack --}}
                <div class="lg:col-span-5 vl-panel p-7 space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-vl-ink">Workspace Runtime Stack</h2>
                        <span class="font-mono text-xs text-vl-ink-muted tabular-nums">{{ count($techStack) }} runtimes</span>
                    </div>

                    @if(count($techStack) > 0)
                        @php $maxCount = max(1, collect($techStack)->max('count')); @endphp
                        <div class="space-y-3.5">
                            @foreach($techStack as $stack)
                                @php
                                    $pct = min(100, (int) round(($stack->count / $maxCount) * 100));
                                @endphp
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-vl-ink">{{ ucfirst($stack->language) }}</span>
                                        <span class="font-mono text-xs text-vl-ink-muted tabular-nums">{{ $stack->count }} {{ \Illuminate\Support\Str::plural('lab', $stack->count) }}</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-vl-surface-raised overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-vl-accent to-vl-teal" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-vl-ink-secondary">No public workspace runtimes recorded yet.</p>
                    @endif
                </div>

                {{-- Coding Schedule / Rhythm --}}
                <div class="lg:col-span-7 vl-panel p-7 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-base font-bold text-vl-ink">Laboratory Activity Rhythm</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-vl-teal/12 px-3 py-0.5 text-xs font-semibold text-vl-teal">
                            Peak focus: {{ $dominantTime }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                        @foreach($schedulePercentages as $period => $pct)
                            <div class="rounded-2xl bg-vl-surface-raised/80 p-4 space-y-2">
                                <p class="text-xs font-medium text-vl-ink-secondary">{{ $period }}</p>
                                <p class="font-mono text-2xl font-bold text-vl-ink tabular-nums">{{ $pct }}%</p>
                                <div class="h-1.5 w-full rounded-full bg-vl-border/10 overflow-hidden">
                                    <div class="h-full rounded-full bg-vl-accent" style="width: {{ min(100, max(0, (int) $pct)) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Contribution Activity Heatmap --}}
            <div class="vl-panel p-7 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-vl-ink">Contribution Activity</h2>
                        <p class="text-xs text-vl-ink-muted mt-0.5">Verified container commits and assignment snapshots over the past year</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-vl-ink-muted">
                        <span>Less</span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-surface-raised"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal/30"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal/60"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal"></span>
                        <span>More</span>
                    </div>
                </div>

                @php
                    $today = \Carbon\Carbon::today();
                    $start = $today->copy()->subDays(363)->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $weeks = [];
                    $cursor = $start->copy();
                    while ($cursor->lte($today)) {
                        $week = [];
                        for ($d = 0; $d < 7; $d++) {
                            $dateKey = $cursor->format('Y-m-d');
                            $count = (int) ($heatmapData[$dateKey] ?? 0);
                            $week[] = [
                                'date' => $dateKey,
                                'count' => $count,
                                'future' => $cursor->gt($today),
                            ];
                            $cursor->addDay();
                        }
                        $weeks[] = $week;
                    }
                @endphp

                <div class="overflow-x-auto pb-1">
                    <div class="inline-flex gap-1">
                        @foreach($weeks as $week)
                            <div class="flex flex-col gap-1">
                                @foreach($week as $day)
                                    @php
                                        if ($day['future']) {
                                            $cellClass = 'bg-transparent';
                                        } elseif ($day['count'] >= 6) {
                                            $cellClass = 'bg-vl-teal';
                                        } elseif ($day['count'] >= 3) {
                                            $cellClass = 'bg-vl-teal/65';
                                        } elseif ($day['count'] >= 1) {
                                            $cellClass = 'bg-vl-teal/30';
                                        } else {
                                            $cellClass = 'bg-vl-surface-raised';
                                        }
                                    @endphp
                                    <span
                                        title="{{ $day['date'] }}: {{ $day['count'] }} {{ \Illuminate\Support\Str::plural('contribution', $day['count']) }}"
                                        class="h-3 w-3 rounded-[3px] {{ $cellClass }}"
                                    ></span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Verified Badges & Achievements (Zero Emoji Icons — Uses <x-badge-icon />) --}}
            <div class="vl-panel p-7 space-y-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-vl-ink">Verified Academic Badges</h2>
                    <span class="font-mono text-xs text-vl-ink-muted tabular-nums">{{ count($badges) }} earned</span>
                </div>

                @if(count($badges) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($badges as $badge)
                            <div class="rounded-2xl bg-vl-surface-raised/75 p-4 flex items-start gap-3.5">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-vl-accent/12 text-vl-accent">
                                    <x-badge-icon :icon="$badge->icon ?? 'trophy'" class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $badge->name }}</p>
                                    <p class="text-xs leading-relaxed text-vl-ink-secondary mt-0.5">{{ $badge->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-vl-ink-secondary">No achievement badges unlocked yet.</p>
                @endif
            </div>
        </div>
    </section>
@endsection
