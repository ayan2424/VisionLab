@extends('layouts.admin')
@section('title', 'Analytics')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-white">Analytics</h1>
        <p class="text-slate-500 text-sm mt-1">Platform usage — last 30 days</p>
    </div>
</div>

{{-- Summary stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Users',       'value' => $stats['total_users'],       'color' => 'text-violet-400'],
        ['label' => 'Total Courses',      'value' => $stats['total_courses'],      'color' => 'text-cyan-400'],
        ['label' => 'Total Submissions',  'value' => $stats['total_submissions'],  'color' => 'text-emerald-400'],
        ['label' => 'Pending Grading',    'value' => $stats['pending_grading'],    'color' => 'text-amber-400'],
        ['label' => 'AI Actions Today',   'value' => $stats['ai_actions_today'],   'color' => 'text-rose-400'],
        ['label' => 'Total AI Actions',   'value' => $stats['ai_actions_total'],   'color' => 'text-blue-400'],
    ] as $i => $stat)
    <div class="rounded-2xl border border-white/[0.07] p-5" style="background:#111111;animation:fadeSlideUp .3s ease both;animation-delay:{{ $i*60 }}ms;">
        <div class="text-3xl font-black {{ $stat['color'] }} stat-counter mb-1" data-target="{{ $stat['value'] }}">{{ $stat['value'] }}</div>
        <div class="text-xs text-slate-500">{{ $stat['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- User Registrations --}}
    <div class="rounded-2xl border border-white/[0.07] p-5" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">New User Registrations</h3>
        <div class="relative h-52">
            <canvas id="userChart"></canvas>
        </div>
    </div>
    {{-- Submissions --}}
    <div class="rounded-2xl border border-white/[0.07] p-5" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">Daily Submissions</h3>
        <div class="relative h-52">
            <canvas id="submissionChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- AI Actions --}}
    <div class="rounded-2xl border border-white/[0.07] p-5 lg:col-span-2" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">AI Actions Per Day</h3>
        <div class="relative h-52">
            <canvas id="aiChart"></canvas>
        </div>
    </div>
    {{-- AI Mode Distribution --}}
    <div class="rounded-2xl border border-white/[0.07] p-5" style="background:#111111;">
        <h3 class="text-sm font-bold text-white mb-4">AI Mode Usage</h3>
        <div class="relative h-52 flex items-center justify-center">
            @if(array_sum($aiModes) > 0)
            <canvas id="modeChart"></canvas>
            @else
            <div class="text-center">
                <div class="text-slate-500 text-sm mb-2">No AI sessions yet</div>
                <div class="grid grid-cols-3 gap-2 mt-4">
                    @foreach([['CHAT','text-violet-400'],['PLAN','text-cyan-400'],['AGENT','text-amber-400']] as $mode)
                    <div class="text-center p-3 rounded-xl border border-white/[0.06]">
                        <div class="text-lg font-black {{ $mode[1] }}">0</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $mode[0] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Recent AI Actions Table --}}
<div class="rounded-2xl border border-white/[0.07] p-5" style="background:#111111;">
    <h3 class="text-sm font-bold text-white mb-4">Recent AI Actions Log</h3>
    @if($recentActions->isEmpty())
    <div class="text-center py-8 text-slate-500 text-sm">No AI actions logged yet. Use the AI agent in a workspace to see logs.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/[0.06]">
                    <th class="text-left pb-3 text-xs font-semibold text-slate-400">User</th>
                    <th class="text-left pb-3 text-xs font-semibold text-slate-400">Action</th>
                    <th class="text-left pb-3 text-xs font-semibold text-slate-400">Mode</th>
                    <th class="text-left pb-3 text-xs font-semibold text-slate-400">File</th>
                    <th class="text-right pb-3 text-xs font-semibold text-slate-400">Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentActions as $action)
                <tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                    <td class="py-2.5 pr-4 text-sm text-white">{{ $action->user->name ?? 'Unknown' }}</td>
                    <td class="py-2.5 pr-4 text-xs text-slate-400">{{ $action->action_type }}</td>
                    <td class="py-2.5 pr-4">
                        <span class="px-2 py-0.5 rounded-md text-xs border
                            {{ $action->mode === 'AGENT' ? 'text-amber-400 bg-amber-400/10 border-amber-400/20' : ($action->mode === 'PLAN' ? 'text-cyan-400 bg-cyan-400/10 border-cyan-400/20' : 'text-violet-400 bg-violet-400/10 border-violet-400/20') }}">
                            {{ $action->mode }}
                        </span>
                    </td>
                    <td class="py-2.5 pr-4 text-xs text-slate-500 font-mono">{{ $action->file_path ?? '—' }}</td>
                    <td class="py-2.5 text-xs text-slate-500 text-right">{{ $action->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
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
        x: {
            ticks: { color: '#475569', font: { size: 10 }, maxTicksLimit: 8 },
            grid: { color: 'rgba(255,255,255,0.04)' }
        },
        y: {
            ticks: { color: '#475569', font: { size: 10 } },
            grid: { color: 'rgba(255,255,255,0.04)' },
            beginAtZero: true
        }
    }
};

// User chart
new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($userChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($userChart['values']) !!},
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(240,80,0,.12)',
            borderWidth: 2, fill: true, tension: 0.4,
            pointBackgroundColor: '#8b5cf6', pointRadius: 3
        }]
    },
    options: { ...chartDefaults }
});

// Submission chart
new Chart(document.getElementById('submissionChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($submissionChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($submissionChart['values']) !!},
            backgroundColor: 'rgba(6,182,212,.6)',
            borderColor: '#06b6d4', borderWidth: 1, borderRadius: 4
        }]
    },
    options: { ...chartDefaults }
});

// AI chart
new Chart(document.getElementById('aiChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($aiChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($aiChart['values']) !!},
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245,158,11,.1)',
            borderWidth: 2, fill: true, tension: 0.4,
            pointBackgroundColor: '#f59e0b', pointRadius: 3
        }]
    },
    options: { ...chartDefaults }
});

@if(array_sum($aiModes) > 0)
// Mode doughnut
new Chart(document.getElementById('modeChart'), {
    type: 'doughnut',
    data: {
        labels: ['CHAT', 'PLAN', 'AGENT'],
        datasets: [{
            data: [
                {!! $aiModes['CHAT'] ?? 0 !!},
                {!! $aiModes['PLAN'] ?? 0 !!},
                {!! $aiModes['AGENT'] ?? 0 !!}
            ],
            backgroundColor: ['rgba(240,80,0,.8)', 'rgba(6,182,212,.8)', 'rgba(245,158,11,.8)'],
            borderColor: ['#8b5cf6', '#06b6d4', '#f59e0b'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#94a3b8', font: { size: 11 }, padding: 12 }
            }
        }
    }
});
@endif

// Count-up
document.querySelectorAll('.stat-counter').forEach(el => {
    const target = parseInt(el.dataset.target);
    if (!target) return;
    let n = 0; const step = Math.ceil(target / 25);
    const t = setInterval(() => {
        n = Math.min(n + step, target);
        el.textContent = n;
        if (n >= target) clearInterval(t);
    }, 40);
});
</script>
@endsection
