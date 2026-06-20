@extends('layouts.app')
@section('title', 'My Analytics')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold" style="color:var(--vc-text);">My Analytics</h1>
        <p class="text-sm mt-1" style="color:var(--vc-muted);">Track your coding activity and AI interactions.</p>
    </div>

    {{-- KPI Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="rounded-2xl border border-white/[0.07] p-6 relative overflow-hidden" style="background:#111111;">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full blur-2xl opacity-20" style="background:#06b6d4"></div>
            <div class="text-4xl font-black mb-1 text-cyan-400 stat-counter" data-target="{{ $executions }}">0</div>
            <div class="text-sm text-slate-400 font-medium">Terminal Commands Executed</div>
        </div>

        <div class="rounded-2xl border border-white/[0.07] p-6 relative overflow-hidden" style="background:#111111;">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full blur-2xl opacity-20" style="background:#8b5cf6"></div>
            <div class="text-4xl font-black mb-1 text-purple-400 stat-counter" data-target="{{ $aiInteractions }}">0</div>
            <div class="text-sm text-slate-400 font-medium">AI Agent Interactions</div>
        </div>

        <div class="rounded-2xl border border-white/[0.07] p-6 relative overflow-hidden" style="background:#111111;">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full blur-2xl opacity-20" style="background:#10b981"></div>
            <div class="text-4xl font-black mb-1 text-emerald-400 stat-counter" data-target="{{ $activeSessions }}">0</div>
            <div class="text-sm text-slate-400 font-medium">Active Workspaces</div>
        </div>
    </div>

    {{-- Activity Chart --}}
    <div class="rounded-2xl border border-white/[0.07] p-6 mb-8" style="background:#111111;">
        <h3 class="text-lg font-bold text-white mb-6">Activity Trends (Last 14 Days)</h3>
        <div class="relative h-64">
            <canvas id="activityChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartDefaults = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { ticks: { color: '#475569', font: { size: 11 }, maxTicksLimit: 14 }, grid: { color: 'rgba(255,255,255,0.04)' } },
        y: { ticks: { color: '#475569', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.04)' }, beginAtZero: true }
    }
};

new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($activityChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($activityChart['values']) !!},
            borderColor: '#06b6d4', backgroundColor: 'rgba(6,182,212,.12)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3
        }]
    },
    options: { ...chartDefaults }
});

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
