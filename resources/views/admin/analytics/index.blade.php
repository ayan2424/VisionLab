@extends('layouts.admin')
@section('title', 'Admin Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="space-y-6">
    <div class="space-y-1">
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Platform Analytics</h2>
        <p class="text-sm text-vl-ink-secondary">Longitudinal registration, workspace launch, submission, and AI governance telemetry.</p>
    </div>

    {{-- Tabular KPI Strip --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Users" :value="number_format((int) $stats['total_users'])" tone="accent" />
        <x-stat-card label="Active Workspaces" :value="number_format((int) $stats['total_workspaces'])" tone="info" />
        <x-stat-card label="AI Actions Logged" :value="number_format((int) $stats['ai_actions_total'])" tone="warning" />
        <x-stat-card label="Telemetry Events" :value="number_format((int) $stats['telemetry_events'])" tone="success" />
    </div>

    {{-- Registrations & Role Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card title="Daily Registrations (Last 30 Days)" :padding="true" class="lg:col-span-2">
            <div class="relative h-56 w-full"><canvas id="userChart"></canvas></div>
        </x-card>

        <x-card title="Users by Role" :padding="true">
            <div class="relative h-56 w-full flex items-center justify-center">
                @if(array_sum($roleDistribution) > 0)
                    <canvas id="roleChart"></canvas>
                @else
                    <p class="text-sm text-vl-ink-muted">No role distribution data.</p>
                @endif
            </div>
        </x-card>
    </div>

    {{-- AI Actions & AI Modes --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card title="AI Actions (Last 30 Days)" :padding="true" class="lg:col-span-2">
            <div class="relative h-56 w-full"><canvas id="aiChart"></canvas></div>
        </x-card>

        <x-card title="AI Mode Usage" :padding="true">
            <div class="relative h-56 w-full flex items-center justify-center">
                @if(array_sum($aiModes) > 0)
                    <canvas id="modeChart"></canvas>
                @else
                    <p class="text-sm text-vl-ink-muted">No AI mode usage recorded.</p>
                @endif
            </div>
        </x-card>
    </div>

    {{-- Submissions & Workspaces --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Submissions (Last 30 Days)" :padding="true">
            <div class="relative h-56 w-full"><canvas id="submissionChart"></canvas></div>
        </x-card>

        <x-card title="Workspaces Started (Last 14 Days)" :padding="true">
            <div class="relative h-56 w-full"><canvas id="workspaceChart"></canvas></div>
        </x-card>
    </div>

    {{-- Event Taxonomy & Recent Telemetry Table --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-card title="Top Event Taxonomy" :padding="true">
            <div class="relative h-56 w-full flex items-center justify-center">
                @if(array_sum($eventTypes) > 0)
                    <canvas id="eventChart"></canvas>
                @else
                    <p class="text-sm text-vl-ink-muted">No telemetry events logged.</p>
                @endif
            </div>
        </x-card>

        <x-card title="Recent Telemetry Log" :padding="false" class="lg:col-span-2 overflow-hidden">
            @if($recentEvents->isEmpty())
                <div class="p-8 text-center text-sm text-vl-ink-muted">No telemetry events logged yet.</div>
            @else
                <div class="max-h-[300px] overflow-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="sticky top-0 bg-vl-surface-raised border-b border-vl-border/10 text-vl-ink-secondary">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Time</th>
                                <th class="px-5 py-3 font-semibold">User</th>
                                <th class="px-5 py-3 font-semibold">Event</th>
                                <th class="px-5 py-3 text-right font-semibold">Resource</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-vl-border/10">
                            @foreach($recentEvents as $ev)
                                <tr class="hover:bg-vl-surface-raised/50 transition-colors">
                                    <td class="px-5 py-2.5 font-mono text-vl-ink-muted tabular-nums">{{ $ev->created_at->format('M d, H:i:s') }}</td>
                                    <td class="px-5 py-2.5 font-medium text-vl-ink">{{ collect($ev->user)->get('name', 'Guest') }}</td>
                                    <td class="px-5 py-2.5 font-mono text-vl-accent">{{ $ev->event_type }}</td>
                                    <td class="px-5 py-2.5 font-mono text-vl-ink-muted text-right">{{ collect($ev->resource_type)->last() ?? '—' }} #{{ collect($ev->resource_id)->last() ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(() => {
    const datasets = {
        user: @json($userChart),
        submission: @json($submissionChart),
        ai: @json($aiChart),
        workspace: @json($workspaceChart),
        aiModes: @json($aiModes),
        roles: @json($roleDistribution),
        events: @json($eventTypes),
    };

    const instances = [];

    function readTokens() {
        const s = getComputedStyle(document.documentElement);
        return {
            accent: `rgb(${s.getPropertyValue('--vl-accent').trim()})`,
            accentFill: `rgb(${s.getPropertyValue('--vl-accent').trim()} / 0.14)`,
            teal: `rgb(${s.getPropertyValue('--vl-teal').trim()})`,
            tealFill: `rgb(${s.getPropertyValue('--vl-teal').trim()} / 0.72)`,
            gate: `rgb(${s.getPropertyValue('--vl-gate').trim()})`,
            gateFill: `rgb(${s.getPropertyValue('--vl-gate').trim()} / 0.16)`,
            danger: `rgb(${s.getPropertyValue('--vl-danger').trim()})`,
            cta: `rgb(${s.getPropertyValue('--vl-accent-cta').trim()})`,
            muted: `rgb(${s.getPropertyValue('--vl-ink-muted').trim()})`,
            grid: `rgb(${s.getPropertyValue('--vl-border').trim()} / 0.08)`,
        };
    }

    function renderAll() {
        if (!window.Chart) return;
        while (instances.length) instances.pop().destroy();
        const t = readTokens();

        const baseOpts = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: t.muted, font: { size: 10 }, maxTicksLimit: 8 }, grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0, color: t.muted, font: { size: 10 } }, grid: { color: t.grid } },
            },
        };

        const mk = (id, cfg) => {
            const el = document.getElementById(id);
            if (el) instances.push(new Chart(el, cfg));
        };

        mk('userChart', {
            type: 'line',
            data: { labels: datasets.user.labels, datasets: [{ data: datasets.user.values, borderColor: t.accent, backgroundColor: t.accentFill, borderWidth: 2, fill: true, tension: 0.35, pointRadius: 2 }] },
            options: baseOpts,
        });

        mk('submissionChart', {
            type: 'bar',
            data: { labels: datasets.submission.labels, datasets: [{ data: datasets.submission.values, backgroundColor: t.tealFill, borderRadius: 5 }] },
            options: baseOpts,
        });

        mk('aiChart', {
            type: 'line',
            data: { labels: datasets.ai.labels, datasets: [{ data: datasets.ai.values, borderColor: t.gate, backgroundColor: t.gateFill, borderWidth: 2, fill: true, tension: 0.35, pointRadius: 2 }] },
            options: baseOpts,
        });

        mk('workspaceChart', {
            type: 'bar',
            data: { labels: datasets.workspace.labels, datasets: [{ data: datasets.workspace.values, backgroundColor: t.accent, borderRadius: 5 }] },
            options: baseOpts,
        });

        if (Object.values(datasets.aiModes).reduce((a, b) => a + b, 0) > 0) {
            mk('modeChart', {
                type: 'doughnut',
                data: { labels: Object.keys(datasets.aiModes), datasets: [{ data: Object.values(datasets.aiModes), backgroundColor: [t.accent, t.teal, t.gate, t.danger], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { position: 'bottom', labels: { color: t.muted } } } },
            });
        }

        if (Object.values(datasets.roles).reduce((a, b) => a + b, 0) > 0) {
            mk('roleChart', {
                type: 'doughnut',
                data: { labels: Object.keys(datasets.roles), datasets: [{ data: Object.values(datasets.roles), backgroundColor: [t.danger, t.teal, t.cta, t.gate], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '64%', plugins: { legend: { position: 'bottom', labels: { color: t.muted } } } },
            });
        }

        if (Object.values(datasets.events).reduce((a, b) => a + b, 0) > 0) {
            mk('eventChart', {
                type: 'polarArea',
                data: { labels: Object.keys(datasets.events), datasets: [{ data: Object.values(datasets.events), backgroundColor: [t.accent, t.teal, t.gate, t.danger, t.cta], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });
        }
    }

    document.addEventListener('DOMContentLoaded', renderAll);
    window.addEventListener('vl:theme-changed', renderAll);
})();
</script>
@endpush
