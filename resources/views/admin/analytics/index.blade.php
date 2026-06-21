@extends('layouts.admin')
@section('title', 'Admin Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="flex items-center justify-between mb-6" style="opacity:0;animation:fadeSlideUp .5s .05s ease forwards">
    <h1 class="text-xl font-bold" style="color:var(--vc-text);">Platform Analytics</h1>
</div>

{{-- KPI Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" style="opacity:0;animation:fadeSlideUp .5s .1s ease forwards">
    @php
        $kpis = [
            ['label' => 'Total Users', 'value' => $stats['total_users'], 'color' => '#8b5cf6'],
            ['label' => 'Active Workspaces', 'value' => $stats['total_workspaces'], 'color' => '#06b6d4'],
            ['label' => 'AI Actions Logged', 'value' => $stats['ai_actions_total'], 'color' => '#f59e0b'],
            ['label' => 'Telemetry Events', 'value' => $stats['telemetry_events'], 'color' => '#ef4444'],
        ];
    @endphp
    @foreach($kpis as $stat)
    <div class="rounded-2xl border border-white/[0.07] p-5 relative overflow-hidden group" style="background:#111111;">
        <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full blur-2xl transition-opacity opacity-20 group-hover:opacity-40" style="background:{{ $stat['color'] }}"></div>
        <div class="text-3xl font-black mb-1" style="color:var(--vc-text);">
            <span class="stat-counter" data-target="{{ (int)$stat['value'] }}">0</span>
        </div>
        <div class="text-xs text-slate-500">{{ $stat['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- User Registrations --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 lg:col-span-2 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">Daily Registrations (Last 30 Days)</h3>
        <div class="relative h-52 w-full">
            <canvas id="userChart"></canvas>
        </div>
    </div>

    {{-- Role Distribution --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">Users by Role</h3>
        <div class="relative h-52 w-full flex items-center justify-center">
            @if(array_sum($roleDistribution) > 0)
            <canvas id="roleChart"></canvas>
            @else
            <div class="text-center text-slate-500 text-sm">No data</div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- AI Actions --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 lg:col-span-2 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">AI Actions (Last 30 Days)</h3>
        <div class="relative h-52 w-full">
            <canvas id="aiChart"></canvas>
        </div>
    </div>

    {{-- AI Mode Distribution --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">AI Mode Usage</h3>
        <div class="relative h-52 w-full flex items-center justify-center">
            @if(array_sum($aiModes) > 0)
            <canvas id="modeChart"></canvas>
            @else
            <div class="text-center text-slate-500 text-sm">No AI data</div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Submissions --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">Submissions (Last 30 Days)</h3>
        <div class="relative h-52 w-full">
            <canvas id="submissionChart"></canvas>
        </div>
    </div>

    {{-- Workspaces --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">Workspaces Started (Last 14 Days)</h3>
        <div class="relative h-52 w-full">
            <canvas id="workspaceChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Event Taxonomy --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 lg:col-span-1 min-w-0 min-h-0" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">Top Event Taxonomy</h3>
        <div class="relative h-52 w-full flex items-center justify-center">
            @if(array_sum($eventTypes) > 0)
            <canvas id="eventChart"></canvas>
            @else
            <div class="text-center text-slate-500 text-sm">No events logged</div>
            @endif
        </div>
    </div>

    {{-- Recent Telemetry Events Table --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 lg:col-span-2 overflow-auto" style="background:#111111;max-height:300px;">
        <h3 class="text-sm font-bold text-white mb-4">Recent Telemetry Log</h3>
        @if($recentEvents->isEmpty())
        <div class="text-center py-8 text-slate-500 text-sm">No telemetry events logged yet.</div>
        @else
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/[0.06]">
                    <th class="text-left pb-2 text-xs font-semibold text-slate-400">Time</th>
                    <th class="text-left pb-2 text-xs font-semibold text-slate-400">User</th>
                    <th class="text-left pb-2 text-xs font-semibold text-slate-400">Event</th>
                    <th class="text-right pb-2 text-xs font-semibold text-slate-400">Resource</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentEvents as $ev)
                <tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                    <td class="py-2 pr-4 text-xs text-slate-500">{{ $ev->created_at->format('M d, H:i:s') }}</td>
                    <td class="py-2 pr-4 text-sm text-white">{{ collect($ev->user)->get('name', 'Guest') }}</td>
                    <td class="py-2 pr-4 text-xs font-mono text-[#3b82f6]">{{ $ev->event_type }}</td>
                    <td class="py-2 text-xs text-slate-500 text-right">{{ collect($ev->resource_type)->last() ?? '—' }} #{{ collect($ev->resource_id)->last() ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartDefaults = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { ticks: { color: '#475569', font: { size: 10 }, maxTicksLimit: 8 }, grid: { color: 'rgba(255,255,255,0.04)' } },
        y: { ticks: { color: '#475569', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' }, beginAtZero: true }
    }
};

// 1. User chart
new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($userChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($userChart['values']) !!},
            borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,.12)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 2
        }]
    },
    options: { ...chartDefaults }
});

// 2. Submission chart
new Chart(document.getElementById('submissionChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($submissionChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($submissionChart['values']) !!},
            backgroundColor: 'rgba(16,185,129,.6)', borderColor: '#10b981', borderWidth: 1, borderRadius: 4
        }]
    },
    options: { ...chartDefaults }
});

// 3. AI chart
new Chart(document.getElementById('aiChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($aiChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($aiChart['values']) !!},
            borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 2
        }]
    },
    options: { ...chartDefaults }
});

// 4. Workspace chart
new Chart(document.getElementById('workspaceChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($workspaceChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($workspaceChart['values']) !!},
            backgroundColor: 'rgba(6,182,212,.6)', borderColor: '#06b6d4', borderWidth: 1, borderRadius: 4
        }]
    },
    options: { ...chartDefaults }
});

@if(array_sum($aiModes) > 0)
// 5. Mode doughnut
new Chart(document.getElementById('modeChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($aiModes)) !!},
        datasets: [{
            data: {!! json_encode(array_values($aiModes)) !!},
            backgroundColor: ['#8b5cf6', '#06b6d4', '#f59e0b', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } } }
});
@endif

@if(array_sum($roleDistribution) > 0)
// 6. Role doughnut
new Chart(document.getElementById('roleChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($roleDistribution)) !!},
        datasets: [{
            data: {!! json_encode(array_values($roleDistribution)) !!},
            backgroundColor: ['#ef4444', '#10b981', '#3b82f6', '#f59e0b'],
            borderWidth: 0
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } } }
});
@endif

@if(array_sum($eventTypes) > 0)
// 7. Event Taxonomy
new Chart(document.getElementById('eventChart'), {
    type: 'polarArea',
    data: {
        labels: {!! json_encode(array_keys($eventTypes)) !!},
        datasets: [{
            data: {!! json_encode(array_values($eventTypes)) !!},
            backgroundColor: [
                'rgba(59,130,246,0.6)', 'rgba(16,185,129,0.6)', 'rgba(245,158,11,0.6)', 
                'rgba(239,68,68,0.6)', 'rgba(139,92,246,0.6)', 'rgba(6,182,212,0.6)'
            ],
            borderWidth: 0
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
});
@endif

// Count-up
document.querySelectorAll('.stat-counter').forEach(el => {
    const target = parseInt(el.dataset.target);
    if (!target) return;
    let n = 0; const step = Math.ceil(target / 25) || 1;
    const t = setInterval(() => {
        n = Math.min(n + step, target);
        el.textContent = n.toLocaleString();
        if (n >= target) clearInterval(t);
    }, 40);
});
</script>
@endsection
