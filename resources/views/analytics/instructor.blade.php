@extends('layouts.app')
@section('title', 'Course Analytics')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold" style="color:var(--vc-text);">Course Analytics</h1>
        <p class="text-sm mt-1" style="color:var(--vc-muted);">Track student engagement and activities across your courses.</p>
    </div>

    {{-- KPI Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="rounded-2xl border border-white/[0.07] p-6 relative overflow-hidden" style="background:#111111;">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full blur-2xl opacity-20" style="background:#3b82f6"></div>
            <div class="text-4xl font-black mb-1 text-blue-400 stat-counter" data-target="{{ $executions }}">0</div>
            <div class="text-sm text-slate-400 font-medium">Student Commands Executed</div>
        </div>

        <div class="rounded-2xl border border-white/[0.07] p-6 relative overflow-hidden" style="background:#111111;">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full blur-2xl opacity-20" style="background:#8b5cf6"></div>
            <div class="text-4xl font-black mb-1 text-purple-400 stat-counter" data-target="{{ $aiInteractions }}">0</div>
            <div class="text-sm text-slate-400 font-medium">Student AI Interactions</div>
        </div>

        <div class="rounded-2xl border border-white/[0.07] p-6 relative overflow-hidden" style="background:#111111;">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full blur-2xl opacity-20" style="background:#f59e0b"></div>
            <div class="text-4xl font-black mb-1 text-amber-400 stat-counter" data-target="{{ $pendingGrading }}">0</div>
            <div class="text-sm text-slate-400 font-medium">Submissions Pending Grading</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Activity Chart --}}
        <div class="rounded-2xl border border-white/[0.07] p-6 lg:col-span-2" style="background:#111111;">
            <h3 class="text-lg font-bold text-white mb-6">Student Activity (Last 14 Days)</h3>
            <div class="relative h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        {{-- Recent Activity Log --}}
        <div class="rounded-2xl border border-white/[0.07] p-6 overflow-auto" style="background:#111111; max-height: 340px;">
            <h3 class="text-sm font-bold text-white mb-4">Live Student Telemetry</h3>
            @if($recentEvents->isEmpty())
            <div class="text-center py-8 text-slate-500 text-sm">No recent activity detected.</div>
            @else
            <div class="space-y-4">
                @foreach($recentEvents as $ev)
                <div class="flex items-start gap-3 border-b border-white/[0.04] pb-3 last:border-0">
                    <div class="w-8 h-8 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                        {{ strtoupper(substr(collect($ev->user)->get('name', 'G'), 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-sm text-white font-medium">{{ collect($ev->user)->get('name', 'Guest') }}</div>
                        <div class="text-xs text-slate-400 font-mono">{{ $ev->event_type }}</div>
                        <div class="text-[10px] text-slate-500 mt-1">{{ $ev->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
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
    type: 'bar',
    data: {
        labels: {!! json_encode($activityChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($activityChart['values']) !!},
            backgroundColor: 'rgba(59,130,246,.6)', borderColor: '#3b82f6', borderWidth: 1, borderRadius: 4
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
