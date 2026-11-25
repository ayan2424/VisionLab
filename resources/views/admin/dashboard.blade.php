@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'System Overview')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">System Overview</h2>
            <p class="text-sm text-vl-ink-secondary">
                Platform health, governance alerts, and institutional metrics &middot; {{ now()->format('M d, Y · H:i') }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.status') }}" class="vl-btn-secondary !py-2 !px-4 !text-xs">System Status</a>
            <a href="{{ route('admin.analytics') }}" class="vl-btn-primary !py-2 !px-4 !text-xs">Full Analytics</a>
        </div>
    </div>

    {{-- Live /healthz Infrastructure Probe Strip (Excludes decommissioned jitsi_config in UI) --}}
    <div
        x-data="{
            loading: true,
            latencyMs: null,
            checkedAt: '—',
            checks: {},
            overall: 'probing',
            services: [
                { key: 'database', label: 'Database' },
                { key: 'redis', label: 'Redis' },
                { key: 'reverb', label: 'Reverb' },
                { key: 'storage', label: 'Storage' },
                { key: 'ai_config', label: 'AI Provider' }
            ],
            async probe() {
                this.loading = true;
                const t0 = performance.now();
                try {
                    const res = await fetch('/healthz', { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.latencyMs = Math.max(1, Math.round(performance.now() - t0));
                    const raw = data.checks || {};
                    const filtered = {};
                    this.services.forEach((s) => {
                        if (Object.prototype.hasOwnProperty.call(raw, s.key)) {
                            filtered[s.key] = Boolean(raw[s.key]);
                        }
                    });
                    this.checks = filtered;
                    const values = Object.values(filtered);
                    const criticalDown = filtered.database === false || filtered.redis === false;
                    if (criticalDown) this.overall = 'outage';
                    else if (values.length > 0 && values.every(Boolean)) this.overall = 'healthy';
                    else this.overall = 'degraded';
                    this.checkedAt = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                } catch {
                    this.overall = 'outage';
                    this.latencyMs = null;
                } finally {
                    this.loading = false;
                }
            }
        }"
        x-init="probe()"
        class="vl-panel px-5 py-4 flex flex-wrap items-center justify-between gap-4"
    >
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2.5 text-xs">
            <div class="inline-flex items-center gap-2 font-semibold">
                <span
                    class="h-2 w-2 rounded-full"
                    :class="{
                        'bg-vl-accent animate-pulse': overall === 'probing',
                        'bg-vl-teal': overall === 'healthy',
                        'bg-vl-gate': overall === 'degraded',
                        'bg-vl-danger': overall === 'outage'
                    }"
                ></span>
                <span
                    :class="{
                        'text-vl-ink-secondary': overall === 'probing',
                        'text-vl-teal': overall === 'healthy',
                        'text-vl-gate': overall === 'degraded',
                        'text-vl-danger': overall === 'outage'
                    }"
                    x-text="overall === 'probing' ? 'Probing /healthz...' : (overall === 'healthy' ? 'Infrastructure Healthy' : (overall === 'degraded' ? 'Partial Degradation' : 'Critical Outage'))"
                ></span>
            </div>

            <div class="hidden sm:block h-4 w-px bg-vl-border/15"></div>

            <template x-for="svc in services" :key="svc.key">
                <div class="inline-flex items-center gap-1.5 font-mono text-[11px] text-vl-ink-secondary">
                    <span
                        class="h-1.5 w-1.5 rounded-full"
                        :class="checks[svc.key] === true ? 'bg-vl-teal' : (checks[svc.key] === false ? 'bg-vl-danger' : 'bg-vl-border/35')"
                    ></span>
                    <span x-text="svc.label"></span>
                </div>
            </template>
        </div>

        <div class="flex items-center gap-3 font-mono text-[11px] text-vl-ink-muted tabular-nums">
            <span>PHP {{ PHP_VERSION }}</span>
            <span class="text-vl-border/30">&middot;</span>
            <span x-show="latencyMs !== null" x-text="latencyMs + 'ms'"></span>
            <span x-text="checkedAt"></span>
            <button
                type="button"
                @click="probe()"
                :disabled="loading"
                class="rounded-lg bg-vl-surface-raised hover:bg-vl-accent/12 text-vl-ink-secondary hover:text-vl-ink px-2.5 py-1 font-sans text-xs font-semibold transition-colors cursor-pointer disabled:opacity-50"
            >
                Re-probe
            </button>
        </div>
    </div>

    {{-- Primary Tabular KPI Strip --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Users" :value="number_format($stats['total_users'])" :subtitle="$stats['total_students'] . ' students · ' . $stats['total_instructors'] . ' faculty'" tone="neutral" />
        <x-stat-card label="Active Courses" :value="number_format($stats['active_courses'])" :subtitle="$stats['total_courses'] . ' total courses'" tone="accent" />
        <x-stat-card label="Pending Grading" :value="number_format($stats['pending_grading'])" :subtitle="$stats['graded_today'] . ' graded today'" :tone="$stats['pending_grading'] > 0 ? 'warning' : 'neutral'" />
        <x-stat-card label="AI Actions Today" :value="number_format($stats['ai_actions_today'])" :subtitle="number_format($stats['ai_actions_total']) . ' lifetime logged'" tone="info" />
    </div>

    {{-- Secondary Operations & Security Ledger --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="vl-panel px-4 py-3.5 flex items-center justify-between">
            <span class="text-xs text-vl-ink-secondary">Running Workspaces</span>
            <span class="font-mono text-base font-bold text-vl-teal tabular-nums">{{ $stats['active_workspaces'] ?? 0 }}</span>
        </div>
        <div class="vl-panel px-4 py-3.5 flex items-center justify-between">
            <span class="text-xs text-vl-ink-secondary">AI Patches Pending</span>
            <span class="font-mono text-base font-bold tabular-nums {{ ($stats['ai_patches_pending'] ?? 0) > 0 ? 'text-vl-gate' : 'text-vl-ink' }}">{{ $stats['ai_patches_pending'] ?? 0 }}</span>
        </div>
        <div class="vl-panel px-4 py-3.5 flex items-center justify-between">
            <span class="text-xs text-vl-ink-secondary">Suspended Users</span>
            <span class="font-mono text-base font-bold tabular-nums {{ ($stats['suspended_users'] ?? 0) > 0 ? 'text-vl-danger' : 'text-vl-ink' }}">{{ $stats['suspended_users'] ?? 0 }}</span>
        </div>
        <div class="vl-panel px-4 py-3.5 flex items-center justify-between">
            <span class="text-xs text-vl-ink-secondary">Verified Extensions</span>
            <span class="font-mono text-base font-bold text-vl-ink tabular-nums">{{ $stats['extensions_active'] ?? 0 }}</span>
        </div>
        <div class="vl-panel px-4 py-3.5 flex items-center justify-between col-span-2 lg:col-span-1">
            <span class="text-xs text-vl-ink-secondary">Admins</span>
            <span class="font-mono text-base font-bold text-vl-ink tabular-nums">{{ $stats['total_admins'] ?? 0 }}</span>
        </div>
    </div>

    {{-- Real Data Charts (Theme-Reactive Chart.js) --}}
    @php
        $signupLabels = collect(array_keys($signupTrend ?? []))->map(fn ($d) => \Carbon\Carbon::parse($d)->format('M d'))->values()->all();
        $signupCounts = array_values($signupTrend ?? []);
        $hasSignupData = array_sum($signupCounts) > 0;

        $aiLabels = $aiActionsTrend['labels'] ?? [];
        $aiCounts = $aiActionsTrend['counts'] ?? [];
        $hasAiData = array_sum($aiCounts) > 0;
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Platform Growth (7-Day Signups)" :padding="true">
            @if($hasSignupData)
                <div class="relative h-60 w-full">
                    <canvas id="growthChart"></canvas>
                </div>
            @else
                <div class="h-60 flex items-center justify-center text-sm text-vl-ink-muted">
                    No new user signups recorded in the past 7 days.
                </div>
            @endif
        </x-card>

        <x-card title="AI Operations (6-Month Activity)" :padding="true">
            @if($hasAiData)
                <div class="relative h-60 w-full">
                    <canvas id="aiUsageChart"></canvas>
                </div>
            @else
                <div class="h-60 flex items-center justify-center text-sm text-vl-ink-muted">
                    No AI actions logged in the past 6 months.
                </div>
            @endif
        </x-card>
    </div>

    {{-- Recent Users & Recent AI Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Recent Users" :padding="true">
            <x-slot:actions>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-vl-accent hover:underline">Manage all</a>
            </x-slot:actions>

            <div class="divide-y divide-vl-border/10">
                @forelse($recentUsers as $user)
                    <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-8 w-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold text-white {{ $user->role === 'admin' ? 'bg-vl-danger' : ($user->role === 'instructor' ? 'bg-vl-accent-cta' : 'bg-vl-teal') }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-semibold text-vl-ink truncate">{{ $user->name }}</span>
                                    @if($user->role === 'student' && $user->student_id)
                                        <a href="{{ route('portfolio.show', $user->student_id) }}" target="_blank" rel="noopener" title="Public Portfolio" class="text-vl-ink-muted hover:text-vl-accent">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        </a>
                                    @endif
                                </div>
                                <p class="text-xs text-vl-ink-muted truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <x-badge :variant="$user->role === 'admin' ? 'danger' : ($user->role === 'instructor' ? 'info' : 'success')">
                                {{ ucfirst($user->role) }}
                            </x-badge>
                            @if($user->status === 'suspended')
                                <x-badge variant="danger">Suspended</x-badge>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-vl-ink-muted">No users found.</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Recent AI Actions" :padding="true">
            <x-slot:actions>
                <a href="{{ route('admin.analytics') }}" class="text-xs font-semibold text-vl-accent hover:underline">Analytics</a>
            </x-slot:actions>

            <div class="divide-y divide-vl-border/10">
                @forelse($recentAiActions as $action)
                    <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-8 w-8 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($action->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-vl-ink truncate">{{ $action->user->name ?? 'Unknown' }}</p>
                                <p class="font-mono text-xs text-vl-ink-muted truncate">{{ $action->action_type }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-vl-ink-muted shrink-0">{{ $action->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-vl-ink-muted">No AI actions logged yet.</p>
                @endforelse
            </div>
        </x-card>
    </div>

    {{-- Recent Audit Logs --}}
    @if(isset($recentAuditLogs) && $recentAuditLogs->count() > 0)
        <x-card title="Recent Audit Logs" :padding="false">
            <x-slot:actions>
                <a href="{{ route('admin.audits.index') }}" class="text-xs font-semibold text-vl-accent hover:underline">Full ledger</a>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-vl-surface-raised/65 border-b border-vl-border/10 text-vl-ink-secondary">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Actor</th>
                            <th class="px-5 py-3 font-semibold">Action</th>
                            <th class="px-5 py-3 font-semibold">Target</th>
                            <th class="px-5 py-3 font-semibold">IP Address</th>
                            <th class="px-5 py-3 text-right font-semibold">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-vl-border/10">
                        @foreach($recentAuditLogs as $log)
                            <tr class="hover:bg-vl-surface-raised/45 transition-colors">
                                <td class="px-5 py-3 font-medium text-vl-ink">{{ $log->actor->name ?? 'System' }}</td>
                                <td class="px-5 py-3 font-mono text-vl-accent">{{ $log->action }}</td>
                                <td class="px-5 py-3 font-mono text-vl-ink-secondary">{{ $log->resource_type }}#{{ $log->resource_id }}</td>
                                <td class="px-5 py-3 font-mono text-vl-ink-muted">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-5 py-3 text-right text-vl-ink-muted tabular-nums">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const signupLabels = @json($signupLabels);
    const signupCounts = @json($signupCounts);
    const aiLabels = @json($aiLabels);
    const aiCounts = @json($aiCounts);

    let growthChart = null;
    let aiChart = null;

    function readThemeColors() {
        const s = getComputedStyle(document.documentElement);
        return {
            accent: `rgb(${s.getPropertyValue('--vl-accent').trim()})`,
            accentFill: `rgb(${s.getPropertyValue('--vl-accent').trim()} / 0.14)`,
            teal: `rgb(${s.getPropertyValue('--vl-teal').trim()})`,
            muted: `rgb(${s.getPropertyValue('--vl-ink-muted').trim()})`,
            grid: `rgb(${s.getPropertyValue('--vl-border').trim()} / 0.08)`,
        };
    }

    function renderCharts() {
        if (!window.Chart) return;
        const c = readThemeColors();

        const growthEl = document.getElementById('growthChart');
        if (growthEl) {
            if (growthChart) growthChart.destroy();
            growthChart = new Chart(growthEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: signupLabels,
                    datasets: [{
                        label: 'New Users',
                        data: signupCounts,
                        borderColor: c.accent,
                        backgroundColor: c.accentFill,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: c.muted }, grid: { display: false } },
                        y: { beginAtZero: true, ticks: { precision: 0, color: c.muted }, grid: { color: c.grid } },
                    },
                },
            });
        }

        const aiEl = document.getElementById('aiUsageChart');
        if (aiEl) {
            if (aiChart) aiChart.destroy();
            aiChart = new Chart(aiEl.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: aiLabels,
                    datasets: [{
                        label: 'AI Actions',
                        data: aiCounts,
                        backgroundColor: c.teal,
                        borderRadius: 6,
                        maxBarThickness: 32,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: c.muted }, grid: { display: false } },
                        y: { beginAtZero: true, ticks: { precision: 0, color: c.muted }, grid: { color: c.grid } },
                    },
                },
            });
        }
    }

    document.addEventListener('DOMContentLoaded', renderCharts);
    window.addEventListener('vl:theme-changed', renderCharts);
})();
</script>
@endpush
