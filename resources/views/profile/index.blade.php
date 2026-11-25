@extends('layouts.dashboard')
@section('title', 'Student Profile')
@section('page-title', 'Student Profile')

@section('content')
<div class="space-y-6">
    {{-- Profile Header --}}
    <div class="vl-panel p-6 flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-2xl shrink-0 bg-gradient-to-br from-vl-accent to-vl-teal text-white font-bold text-2xl flex items-center justify-center shadow-vl-panel">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-vl-ink">{{ $user->name }}</h2>
                <p class="text-sm text-vl-ink-secondary mt-0.5">{{ ucfirst($user->role) }} &middot; Joined {{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-5 rounded-2xl bg-vl-surface-raised/80 px-5 py-3.5 shrink-0">
            <div>
                <p class="font-mono text-[10px] uppercase tracking-wider text-vl-ink-muted">Current Rank</p>
                <p class="text-sm font-bold text-vl-ink mt-0.5">{{ $user->rank_title }}</p>
            </div>
            <div class="h-8 w-px bg-vl-border/15"></div>
            <div>
                <p class="font-mono text-[10px] uppercase tracking-wider text-vl-ink-muted">Level {{ $user->level }}</p>
                <p class="font-mono text-sm font-bold text-vl-teal tabular-nums mt-0.5">{{ number_format($user->xp) }} XP</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left Main Column --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Level Progress --}}
            <x-card title="Level Progress" :padding="true">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-vl-ink">Level {{ $user->level }}</span>
                        <span class="font-mono text-xs text-vl-ink-secondary tabular-nums">{{ number_format($user->xp) }} / {{ number_format($nextLevelXp) }} XP</span>
                    </div>
                    <div class="h-2.5 w-full rounded-full bg-vl-surface-raised overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-vl-accent to-vl-teal transition-[width] duration-500 ease-vl-out" style="width: {{ min(100, max(0, (float) $progressPercent)) }}%"></div>
                    </div>
                    <p class="text-xs text-vl-ink-muted tabular-nums">
                        Earn {{ number_format(max(0, $nextLevelXp - $user->xp)) }} more XP to reach Level {{ $user->level + 1 }}.
                    </p>
                </div>
            </x-card>

            {{-- Contribution Heatmap --}}
            <x-card title="Contribution Activity" :padding="true">
                <div id="heatmap-container" class="overflow-x-auto pb-2">
                    <div id="heatmap-loading" class="skeleton h-28 w-full"></div>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-vl-ink-muted">
                    <div id="streak-info" class="tabular-nums">Loading activity summary...</div>
                    <div class="flex items-center gap-1.5 text-[11px]">
                        <span>Less</span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-surface-raised"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal/25"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal/50"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal/75"></span>
                        <span class="h-3 w-3 rounded-[3px] bg-vl-teal"></span>
                        <span>More</span>
                    </div>
                </div>
            </x-card>

            {{-- Earned Badges (Uses <x-badge-icon /> — Zero Raw HTML/Emoji) --}}
            <x-card title="Earned Badges" :padding="true">
                @if($badges->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                        @foreach($badges as $badge)
                            <div class="rounded-2xl bg-vl-surface-raised/70 p-4 flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-vl-accent/14 text-vl-accent">
                                    <x-badge-icon :icon="$badge->icon ?? 'trophy'" class="h-4 w-4" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-vl-ink truncate">{{ $badge->name }}</p>
                                    <p class="text-xs text-vl-ink-secondary mt-0.5 line-clamp-2">{{ $badge->description }}</p>
                                    @if($badge->earned_at)
                                        <p class="font-mono text-[10px] text-vl-ink-muted mt-1">{{ $badge->earned_at->format('M j, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-empty-state
                        title="No badges earned yet"
                        description="Complete course assignments and workspace milestones to unlock academic badges."
                    />
                @endif
            </x-card>
        </div>

        {{-- Right Sidebar Column --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Top Coders Leaderboard --}}
            <x-card title="Top Coders" :padding="true">
                <div class="space-y-2">
                    @foreach($leaderboard as $index => $leader)
                        <div class="flex items-center justify-between gap-2 rounded-xl px-3 py-2 {{ $leader->id === $user->id ? 'bg-vl-accent/12 ring-1 ring-vl-accent/25' : 'hover:bg-vl-surface-raised/60' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="h-6 w-6 rounded-lg shrink-0 flex items-center justify-center font-mono text-xs font-bold tabular-nums {{ $index === 0 ? 'bg-vl-gate/18 text-vl-gate' : 'bg-vl-surface-raised text-vl-ink-secondary' }}">
                                    {{ $index + 1 }}
                                </span>
                                @if($leader->avatar_url)
                                    <img src="{{ $leader->avatar_url }}" alt="{{ $leader->name }}" class="h-6 w-6 rounded-lg object-cover shrink-0">
                                @else
                                    <span class="h-6 w-6 rounded-lg bg-vl-surface-raised text-vl-ink text-[10px] font-bold flex items-center justify-center shrink-0">
                                        {{ $leader->avatar_initials }}
                                    </span>
                                @endif
                                <span class="text-sm font-medium text-vl-ink truncate">{{ $leader->name }}</span>
                            </div>
                            <span class="font-mono text-xs text-vl-ink-muted tabular-nums shrink-0">{{ number_format($leader->xp) }} XP</span>
                        </div>
                    @endforeach
                </div>
            </x-card>

            {{-- Recent XP Activity --}}
            <x-card title="Recent Activity" :padding="true">
                @if($transactions->count() > 0)
                    <div class="divide-y divide-vl-border/10">
                        @foreach($transactions as $tx)
                            <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-vl-ink truncate">{{ $tx->reason }}</p>
                                    <p class="text-xs text-vl-ink-muted">{{ $tx->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="font-mono text-xs font-bold text-vl-teal tabular-nums shrink-0">+{{ $tx->amount }} XP</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-vl-ink-muted py-4 text-center">No recent activity found.</p>
                @endif
            </x-card>

            {{-- Public Portfolio Card --}}
            @if($user->student_id)
                <x-card title="Public Portfolio" subtitle="Share your verified badges, runtime stack, and activity heatmap." :padding="true">
                    <a href="{{ route('portfolio.show', $user->student_id) }}" target="_blank" rel="noopener" class="vl-btn-primary w-full">
                        Open Public Portfolio
                    </a>
                </x-card>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/api/contributions?user_id={{ $user->id }}');
        const data = await response.json();

        const container = document.getElementById('heatmap-container');
        const streakInfo = document.getElementById('streak-info');
        if (!container) return;

        if (streakInfo) {
            streakInfo.innerHTML = `<strong class="text-vl-ink font-semibold">${data.total_contributions || 0}</strong> contributions in the last year &middot; <strong class="text-vl-teal font-semibold">${data.current_streak || 0}d</strong> streak (Max ${data.max_streak || 0}d)`;
        }

        if (!data.heatmap || !data.heatmap.length) {
            container.innerHTML = '<div class="py-6 text-center text-xs text-vl-ink-muted">No contribution history yet.</div>';
            return;
        }

        const weeks = [];
        let currentWeek = [];
        const firstDate = new Date(data.heatmap[0].date);
        for (let i = 0; i < firstDate.getDay(); i++) currentWeek.push(null);

        data.heatmap.forEach((day) => {
            currentWeek.push(day);
            if (currentWeek.length === 7) {
                weeks.push(currentWeek);
                currentWeek = [];
            }
        });
        if (currentWeek.length > 0) weeks.push(currentWeek);

        let html = '<div class="inline-flex gap-1">';
        weeks.forEach((week) => {
            html += '<div class="flex flex-col gap-1">';
            for (let i = 0; i < 7; i++) {
                const day = week[i];
                if (!day) {
                    html += '<div class="h-3.5 w-3.5 rounded-[3px] bg-transparent"></div>';
                    continue;
                }
                let cls = 'bg-vl-surface-raised';
                if (day.level === 1) cls = 'bg-vl-teal/25';
                else if (day.level === 2) cls = 'bg-vl-teal/50';
                else if (day.level === 3) cls = 'bg-vl-teal/75';
                else if (day.level >= 4) cls = 'bg-vl-teal';

                html += `<div class="h-3.5 w-3.5 rounded-[3px] ${cls}" title="${day.count} contributions on ${day.date}"></div>`;
            }
            html += '</div>';
        });
        html += '</div>';

        container.innerHTML = html;
        container.scrollLeft = container.scrollWidth;
    } catch {
        const container = document.getElementById('heatmap-container');
        if (container) {
            container.innerHTML = '<div class="py-6 text-center text-xs text-vl-danger">Failed to load contribution heatmap.</div>';
        }
    }
});
</script>
@endpush
