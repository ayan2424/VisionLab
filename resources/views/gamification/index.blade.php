@extends('layouts.dashboard')
@section('title', 'Badges & XP')
@section('page-title', 'Badges & XP')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Level & XP Overview Banner --}}
    <div class="vl-panel p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="relative shrink-0">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-2xl object-cover shadow-vl-panel">
                @else
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-vl-accent to-vl-teal text-white font-bold text-2xl flex items-center justify-center shadow-vl-panel">
                        {{ $user->avatar_initials }}
                    </div>
                @endif
                <span class="absolute -bottom-2 -right-2 rounded-lg bg-vl-surface px-2 py-0.5 font-mono text-[11px] font-bold text-vl-accent shadow-vl-panel tabular-nums">
                    L{{ $currentLevel }}
                </span>
            </div>

            <div class="space-y-1">
                <h2 class="text-2xl font-bold tracking-tight text-vl-ink">{{ $user->name }}</h2>
                <div class="inline-flex items-center gap-2 text-sm font-medium text-vl-ink-secondary">
                    <x-badge-icon icon="trophy" class="h-4 w-4 text-vl-gate" />
                    <span>{{ $user->rank_title ?? 'Novice Engineer' }}</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 rounded-2xl bg-vl-surface-raised/80 p-5 space-y-2.5">
            <div class="flex items-center justify-between text-xs font-semibold">
                <span class="text-vl-ink">Level {{ $currentLevel }}</span>
                <span class="font-mono text-vl-ink-secondary tabular-nums">{{ number_format($user->xp) }} / {{ number_format($xpForNextLevel) }} XP</span>
            </div>
            <div class="h-2.5 w-full rounded-full bg-vl-surface overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-vl-accent to-vl-teal transition-[width] duration-500 ease-vl-out" style="width: {{ min(100, max(0, (float) $progressPercentage)) }}%"></div>
            </div>
            <p class="text-[11px] text-vl-ink-muted text-right tabular-nums">{{ $progressPercentage }}% toward Level {{ $nextLevel }}</p>
        </div>
    </div>

    {{-- Tabular KPI Strip --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card label="Current Streak" :value="$user->current_streak . ' Days'" tone="warning" />
        <x-stat-card label="Longest Streak" :value="$user->longest_streak . ' Days'" tone="success" />
        <x-stat-card label="Workspaces Launched" :value="$totalWorkspaces" tone="accent" />
    </div>

    {{-- 365-Day Activity Heatmap --}}
    <div
        x-data="{
            loading: true,
            heatmap: [],
            totalContributions: 0,
            async init() {
                try {
                    const response = await fetch('/api/contributions');
                    const data = await response.json();
                    this.heatmap = data.heatmap || [];
                    this.totalContributions = data.total_contributions || 0;
                } catch (e) {
                    this.heatmap = [];
                } finally {
                    this.loading = false;
                }
            }
        }"
        class="vl-panel p-6 space-y-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-base font-bold text-vl-ink">365-Day Activity Heatmap</h3>
            <p class="font-mono text-xs text-vl-ink-muted tabular-nums">
                <span x-text="totalContributions" class="font-bold text-vl-ink"></span> total contributions
            </p>
        </div>

        <div x-show="loading" class="skeleton h-28 w-full"></div>

        <div x-show="!loading" x-cloak class="overflow-x-auto pb-2">
            <div class="inline-grid grid-flow-col gap-1" style="grid-template-rows: repeat(7, minmax(0, 1fr));">
                <template x-for="day in heatmap" :key="day.date">
                    <div
                        class="h-3 w-3 rounded-[3px] transition-colors"
                        :class="{
                            'bg-vl-surface-raised': day.level === 0,
                            'bg-vl-teal/25': day.level === 1,
                            'bg-vl-teal/50': day.level === 2,
                            'bg-vl-teal/75': day.level === 3,
                            'bg-vl-teal': day.level >= 4
                        }"
                        :title="`${day.count} contributions on ${day.date}`"
                    ></div>
                </template>
            </div>
        </div>
    </div>

    {{-- Earned Badges & XP Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <x-card :title="'Unlocked Badges (' . $user->badges->count() . ')'" :padding="true">
            @if($user->badges->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach($user->badges as $badge)
                        <div class="rounded-2xl bg-vl-surface-raised/70 p-4 flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-vl-accent/14 text-vl-accent">
                                <x-badge-icon :icon="$badge->icon ?? $badge->icon_emoji ?? 'trophy'" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-semibold text-vl-ink truncate">{{ $badge->name }}</h4>
                                <p class="text-xs leading-relaxed text-vl-ink-secondary mt-0.5 line-clamp-2">{{ $badge->description }}</p>
                                @if($badge->earned_at)
                                    <p class="font-mono text-[10px] text-vl-ink-muted mt-1.5">{{ $badge->earned_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="No badges unlocked yet"
                    description="Complete assignments and commit container milestones to earn verified badges."
                />
            @endif
        </x-card>

        <x-card title="Recent XP Activity" :padding="true">
            @if($user->xpTransactions->count() > 0)
                <div class="divide-y divide-vl-border/10">
                    @foreach($user->xpTransactions as $tx)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-vl-ink truncate">{{ $tx->reason }}</p>
                                <p class="font-mono text-[11px] text-vl-ink-muted mt-0.5">{{ $tx->created_at->format('M d, g:i A') }}</p>
                            </div>
                            <span class="font-mono text-sm font-bold text-vl-teal tabular-nums shrink-0">+{{ $tx->amount }} XP</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-vl-ink-muted py-6 text-center">No recent XP transactions recorded.</p>
            @endif
        </x-card>
    </div>
</div>
@endsection
