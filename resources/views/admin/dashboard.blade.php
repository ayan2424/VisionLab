@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'System Overview')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8" style="opacity:0;animation:fadeSlideUp .5s .05s ease forwards">
    <div>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">System Overview</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Platform health & analytics — {{ now()->format('M d, Y · h:i A') }}</p>
    </div>
    <a href="{{ route('admin.analytics') }}" class="btn-ghost py-2 px-4 text-xs">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Full Analytics
    </a>
</div>

{{-- System Health Bar --}}
<div class="vc-card p-6 mb-8 flex flex-wrap items-center gap-8" style="opacity:0;animation:fadeSlideUp .4s .1s ease forwards">
    <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-semibold" style="color:#10B981;">All Systems Operational</span>
    </div>
    <div class="flex items-center gap-1.5 text-xs">
        <span style="color:var(--vc-muted);">PHP</span>
        <span class="font-mono" style="color:var(--vc-text-secondary);">{{ PHP_VERSION }}</span>
    </div>
    <div class="flex items-center gap-1.5 text-xs">
        <span style="color:var(--vc-muted);">Laravel</span>
        <span class="font-mono" style="color:var(--vc-text-secondary);">{{ app()->version() }}</span>
    </div>
    <div class="flex items-center gap-1.5 text-xs">
        <span style="color:var(--vc-muted);">DB</span>
        <span class="font-mono" style="color:#10B981;">Connected</span>
    </div>
    <div class="flex items-center gap-1.5 text-xs">
        <span style="color:var(--vc-muted);">Server</span>
        <span class="font-mono" style="color:#10B981;">Running</span>
    </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Users',      'value' => $stats['total_users'],       'sub' => $stats['total_students'].' students',       'color' => 'indigo', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Total Courses',    'value' => $stats['total_courses'],      'sub' => $stats['active_courses'].' active',          'color' => 'cyan',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Pending Grading',  'value' => $stats['pending_grading'],    'sub' => $stats['total_submissions'].' total subs',   'color' => 'amber',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'AI Actions Today', 'value' => $stats['ai_actions_today'],   'sub' => $stats['ai_actions_total'].' total',        'color' => 'emerald','icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
    ] as $i => $stat)
    @php
        // Map colors to specific hex for backgrounds/borders
        $cMap = ['indigo'=>['#8b5cf6','rgba(139,92,246,0.1)','rgba(139,92,246,0.03)'], 'cyan'=>['#06b6d4','rgba(6,182,212,0.1)','rgba(6,182,212,0.03)'], 'amber'=>['#f59e0b','rgba(245,158,11,0.1)','rgba(245,158,11,0.03)'], 'emerald'=>['#10b981','rgba(16,185,129,0.1)','rgba(16,185,129,0.03)']];
        $hex = $cMap[$stat['color']][0];
        $bg = $cMap[$stat['color']][1];
        $grad = $cMap[$stat['color']][2];
    @endphp
    <div class="vc-card group relative overflow-hidden transition-all duration-300"
         style="padding:28px; background:linear-gradient(135deg, var(--vc-surface) 40%, {{ $grad }}); animation:fadeSlideUp .4s ease both;animation-delay:{{ 150 + $i * 80 }}ms;"
         onmouseover="this.style.borderColor='{{ $hex }}'; this.style.boxShadow='0 8px 30px {{ str_replace('0.1', '0.15', $bg) }}'"
         onmouseout="this.style.borderColor='var(--vc-border)'; this.style.boxShadow='none'">
        <div class="absolute top-0 right-0 w-20 h-20 rounded-full blur-2xl transition-colors" style="background:{{ str_replace('0.1', '0.05', $bg) }};"></div>
        <div class="relative">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 group-hover:scale-105 transition-transform"
                 style="background:{{ $bg }};border:1px solid {{ str_replace('0.1', '0.2', $bg) }};">
                <svg class="w-5 h-5" style="color:{{ $hex }};" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}"/></svg>
            </div>
            <div class="text-3xl font-black mb-0.5 stat-counter" style="color:{{ $hex }};" data-target="{{ $stat['value'] }}">{{ $stat['value'] }}</div>
            <div class="text-xs font-semibold mb-0.5" style="color:var(--vc-text);">{{ $stat['label'] }}</div>
            <div class="text-[11px]" style="color:var(--vc-muted);">{{ $stat['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Secondary stats --}}
<div class="grid grid-cols-3 gap-6 mb-10" style="opacity:0;animation:fadeSlideUp .4s .4s ease forwards">
    <div class="vc-card p-6 flex items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-red-500/50" style="background:linear-gradient(135deg, var(--vc-surface) 60%, rgba(239,68,68,0.03));">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(239,68,68,0.1);">
            <svg class="w-4 h-4" style="color:#ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <div class="text-lg font-bold" style="color:#ef4444;">{{ $stats['total_admins'] }}</div>
            <div class="text-[10px]" style="color:var(--vc-muted);">Admins</div>
        </div>
    </div>
    <div class="vc-card p-6 flex items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-violet-500/50" style="background:linear-gradient(135deg, var(--vc-surface) 60%, rgba(139,92,246,0.03));">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(139,92,246,0.1);">
            <svg class="w-4 h-4" style="color:var(--vc-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
            <div class="text-lg font-bold" style="color:var(--vc-accent);">{{ $stats['total_instructors'] }}</div>
            <div class="text-[10px]" style="color:var(--vc-muted);">Instructors</div>
        </div>
    </div>
    <div class="vc-card p-6 flex items-center gap-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-cyan-500/50" style="background:linear-gradient(135deg, var(--vc-surface) 60%, rgba(6,182,212,0.03));">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(6,182,212,0.1);">
            <svg class="w-4 h-4" style="color:#06b6d4;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
        </div>
        <div>
            <div class="text-lg font-bold" style="color:#06b6d4;">{{ $stats['extensions_active'] }}</div>
            <div class="text-[10px]" style="color:var(--vc-muted);">Extensions</div>
        </div>
    </div>
</div>


{{-- Infrastructure & Security Row --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-6 mb-10" style="opacity:0;animation:fadeSlideUp .4s .5s ease forwards">
    @foreach([
        ['label'=>'Suspended Users','value'=>$stats['suspended_users'] ?? 0,'color'=>'#ef4444','icon'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        ['label'=>'Running Workspaces','value'=>$stats['active_workspaces'] ?? 0,'color'=>'#10b981','icon'=>'M5 12h14M12 5l7 7-7 7'],
        ['label'=>'AI Patches Pending','value'=>$stats['ai_patches_pending'] ?? 0,'color'=>'#f59e0b','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ] as $i => $s)
    <div class="vc-card p-6 flex items-center gap-4">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $s['color'] }}12;">
            <svg class="w-4 h-4" style="color:{{ $s['color'] }};" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
        </div>
        <div>
            <div class="text-lg font-bold" style="color:{{ $s['color'] }};">{{ $s['value'] }}</div>
            <div class="text-[10px]" style="color:var(--vc-muted);">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Recent Users --}}
    <div class="vc-card p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold" style="color:var(--vc-text);">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-xs hover:underline" style="color:var(--vc-accent);">View all</a>
        </div>
        @foreach($recentUsers as $user)
        <div class="flex items-center justify-between py-2.5 last:border-0" style="border-bottom:1px solid var(--vc-border);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white"
                     style="background:{{ $user->role === 'admin' ? '#dc2626' : ($user->role === 'instructor' ? 'linear-gradient(135deg,#7c3aed,#8b5cf6)' : '#0891b2') }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm flex items-center gap-2" style="color:var(--vc-text);">
                        {{ $user->name }}
                        @if($user->role === 'student' && $user->student_id)
                        <a href="{{ route('portfolio.show', $user->student_id) }}" target="_blank" title="View Gamification Portfolio" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        @endif
                    </div>
                    <div class="text-xs" style="color:var(--vc-text-secondary);">{{ $user->email }}</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold border"
                      style="{{ $user->role === 'admin' ? 'color:#ef4444;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2)' : ($user->role === 'instructor' ? 'color:var(--vc-accent);background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2)' : 'color:#06b6d4;background:rgba(6,182,212,0.1);border-color:rgba(6,182,212,0.2)') }}">
                    {{ ucfirst($user->role) }}
                </span>
                @if($user->status === 'suspended')
                <span class="text-xs" style="color:#ef4444;">suspended</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Recent AI Actions --}}
    <div class="vc-card p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold" style="color:var(--vc-text);">Recent AI Actions</h3>
            <a href="{{ route('admin.analytics') }}" class="text-xs hover:underline" style="color:var(--vc-accent);">Analytics</a>
        </div>
        @forelse($recentAiActions as $action)
        <div class="flex items-center gap-3 py-2.5 last:border-0" style="border-bottom:1px solid var(--vc-border);">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                 style="background:rgba(240,80,0,0.1);color:var(--vc-accent);">
                {{ strtoupper(substr($action->user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs" style="color:var(--vc-text);">{{ $action->user->name ?? 'Unknown' }}</div>
                <div class="text-xs" style="color:var(--vc-muted);">{{ $action->action_type }} &middot; {{ $action->created_at->diffForHumans() }}</div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-xs" style="color:var(--vc-muted);">No AI actions logged yet.</div>
        @endforelse
    </div>
</div>

{{-- Audit Logs --}}
@if(isset($recentAuditLogs) && $recentAuditLogs->count() > 0)
<div class="vc-card p-8 mt-8 mb-10" style="opacity:0;animation:fadeSlideUp .4s .6s ease forwards">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color:var(--vc-text);">
        <svg class="w-4 h-4 text-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Recent Audit Logs
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom:1px solid var(--vc-border);">
                    <th class="text-left pb-2 text-xs font-semibold" style="color:var(--vc-muted);">Actor</th>
                    <th class="text-left pb-2 text-xs font-semibold" style="color:var(--vc-muted);">Action</th>
                    <th class="text-left pb-2 text-xs font-semibold" style="color:var(--vc-muted);">Target</th>
                    <th class="text-left pb-2 text-xs font-semibold" style="color:var(--vc-muted);">IP</th>
                    <th class="text-right pb-2 text-xs font-semibold" style="color:var(--vc-muted);">Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentAuditLogs as $log)
                <tr style="border-bottom:1px solid var(--vc-border);">
                    <td class="py-2 text-xs" style="color:var(--vc-text);">{{ $log->actor->name ?? 'System' }}</td>
                    <td class="py-2 text-xs" style="color:var(--vc-accent);">{{ $log->action }}</td>
                    <td class="py-2 text-xs font-mono" style="color:var(--vc-text-secondary);">{{ $log->resource_type }}#{{ $log->resource_id }}</td>
                    <td class="py-2 text-xs" style="color:var(--vc-muted);">{{ $log->ip_address ?? '—' }}</td>
                    <td class="py-2 text-xs text-right" style="color:var(--vc-muted);">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10" style="opacity:0;animation:fadeSlideUp .5s .5s ease forwards">
    <div class="vc-card p-8">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--vc-text);">Platform Growth</h3>
        <div class="relative w-full h-64">
            <canvas id="growthChart"></canvas>
        </div>
    </div>
    <div class="vc-card p-8">
        <h3 class="text-sm font-semibold mb-4" style="color:var(--vc-text);">AI Token Usage</h3>
        <div class="relative w-full h-64">
            <canvas id="aiUsageChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.querySelectorAll('.stat-counter').forEach(el => {
    const target = parseInt(el.dataset.target);
    if (target === 0) return;
    let start = 0;
    const step = Math.ceil(target / 30);
    const timer = setInterval(() => {
        start = Math.min(start + step, target);
        el.textContent = start;
        if (start >= target) clearInterval(timer);
    }, 40);
});

// Initialize Chart.js
const ctxGrowth = document.getElementById('growthChart').getContext('2d');
new Chart(ctxGrowth, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'New Users',
            data: [12, 19, 3, 5, 2, 3],
            borderColor: '#ff7a00',
            tension: 0.4
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#fff' } } }, scales: { x: { ticks: { color: '#aaa' } }, y: { ticks: { color: '#aaa' } } } }
});

const ctxAi = document.getElementById('aiUsageChart').getContext('2d');
new Chart(ctxAi, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Tokens (k)',
            data: [120, 190, 300, 500, 200, 300],
            backgroundColor: '#10B981',
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#fff' } } }, scales: { x: { ticks: { color: '#aaa' } }, y: { ticks: { color: '#aaa' } } } }
});
</script>
@endsection


