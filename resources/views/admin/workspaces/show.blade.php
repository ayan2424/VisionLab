@extends('layouts.admin')
@section('title', 'Workspace Details')
@section('page-title', 'Workspace Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.workspaces.index') }}" class="text-xs hover:underline flex items-center gap-1 w-fit" style="color:var(--vc-muted);">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Workspaces
    </a>
</div>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold flex items-center gap-3" style="color:var(--vc-text);">
            {{ $workspace->slug }}
            @if($workspace->status === 'running')
                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500">Running</span>
            @else
                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-500/10 text-gray-400">{{ ucfirst($workspace->status) }}</span>
            @endif
        </h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Owner: {{ $workspace->owner->name }} ({{ $workspace->owner->email }})</p>
    </div>
    <div class="flex gap-2">
        @if($workspace->status === 'running')
        <a href="{{ route('workspace.show', $workspace->id) }}" target="_blank" class="btn-primary" style="background:var(--vc-accent);border-color:var(--vc-accent);">Open Workspace</a>
        <form method="POST" action="{{ route('admin.workspaces.stop', $workspace->id) }}" onsubmit="return confirm('Force stop this workspace?');">
            @csrf
            <button class="btn-primary" style="background:#EF4444;border-color:#DC2626;">Force Stop</button>
        </form>
        @else
        <form method="POST" action="{{ route('workspace.start', $workspace->id) }}">
            @csrf
            <button class="btn-primary" style="background:#10B981;border-color:#059669;">Start Workspace</button>
        </form>
        @endif
        @if($workspace->status !== 'archived')
        <form method="POST" action="{{ route('admin.workspaces.archive', $workspace->id) }}" onsubmit="return confirm('Archive this workspace?');">
            @csrf
            <button class="btn-ghost text-red-400">Archive</button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="vc-card p-5">
        <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--vc-muted);">Details</h3>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt style="color:var(--vc-text-secondary);">Course</dt>
                <dd style="color:var(--vc-text);">{{ $workspace->course->title ?? 'None' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt style="color:var(--vc-text-secondary);">Created</dt>
                <dd style="color:var(--vc-text);">{{ $workspace->created_at->format('M d, Y h:i A') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt style="color:var(--vc-text-secondary);">Last Accessed</dt>
                <dd style="color:var(--vc-text);">{{ $workspace->last_accessed_at ? $workspace->last_accessed_at->format('M d, Y h:i A') : 'Never' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt style="color:var(--vc-text-secondary);">Container Port</dt>
                <dd class="font-mono" style="color:var(--vc-text);">{{ $workspace->container_port ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <div class="vc-card p-5">
        <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--vc-muted);">Audit Log (Recent)</h3>
        @if($auditLogs->isEmpty())
            <p class="text-sm" style="color:var(--vc-muted);">No recent logs found.</p>
        @else
            <ul class="space-y-3 text-sm">
                @foreach($auditLogs as $log)
                <li class="flex items-start gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-1.5"></div>
                    <div>
                        <p style="color:var(--vc-text);">{{ $log->action }}</p>
                        <p class="text-[11px]" style="color:var(--vc-muted);">by {{ $log->actor->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
</div>

@if($workspace->status === 'running')
<div class="vc-card p-5 mb-8" x-data="workspaceMonitor()">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold uppercase tracking-wider" style="color:var(--vc-muted);">Live Resource Monitoring</h3>
        <div class="flex items-center gap-2 text-[10px] font-bold" :class="error ? 'text-red-500' : 'text-emerald-500'">
            <div class="w-2 h-2 rounded-full" :class="error ? 'bg-red-500' : 'bg-emerald-500 animate-pulse'"></div>
            <span x-text="error ? 'Offline' : 'Connected'"></span>
        </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-black/20 rounded-lg p-4 border border-white/5">
            <p class="text-xs mb-1" style="color:var(--vc-text-secondary);">CPU Usage</p>
            <p class="text-xl font-bold font-mono" style="color:var(--vc-text);" x-text="stats.CPUPerc || '0.00%'"></p>
        </div>
        <div class="bg-black/20 rounded-lg p-4 border border-white/5">
            <p class="text-xs mb-1" style="color:var(--vc-text-secondary);">Memory Usage</p>
            <p class="text-xl font-bold font-mono" style="color:var(--vc-text);" x-text="stats.MemPerc || '0.00%'"></p>
            <p class="text-[10px] mt-1" style="color:var(--vc-muted);" x-text="stats.MemUsage || '- / -'"></p>
        </div>
        <div class="bg-black/20 rounded-lg p-4 border border-white/5">
            <p class="text-xs mb-1" style="color:var(--vc-text-secondary);">Network I/O</p>
            <p class="text-sm font-bold font-mono mt-2" style="color:var(--vc-text);" x-text="stats.NetIO || '- / -'"></p>
        </div>
        <div class="bg-black/20 rounded-lg p-4 border border-white/5">
            <p class="text-xs mb-1" style="color:var(--vc-text-secondary);">Block I/O</p>
            <p class="text-sm font-bold font-mono mt-2" style="color:var(--vc-text);" x-text="stats.BlockIO || '- / -'"></p>
        </div>
    </div>
</div>

<script>
function workspaceMonitor() {
    return {
        stats: {},
        error: false,
        init() {
            this.fetchStats();
            setInterval(() => this.fetchStats(), 5000);
        },
        async fetchStats() {
            try {
                const res = await fetch('{{ route("workspace.status", $workspace->id) }}');
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();
                if (data.stats) {
                    this.stats = data.stats;
                    this.error = false;
                } else {
                    this.error = true;
                }
            } catch (err) {
                this.error = true;
            }
        }
    }
}
</script>
@endif
@endsection


