@extends('layouts.admin')
@section('title', 'Workspace Details')
@section('page-title', 'Workspace Details')

@section('content')
<div class="space-y-6">
    <div class="space-y-3">
        <a href="{{ route('admin.workspaces.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-vl-ink-muted hover:text-vl-ink transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Workspaces
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="font-mono text-xl sm:text-2xl font-bold text-vl-ink">{{ $workspace->slug }}</h2>
                    <x-badge :variant="$workspace->status === 'running' ? 'success' : 'neutral'" dot>
                        {{ ucfirst($workspace->status) }}
                    </x-badge>
                </div>
                <p class="text-sm text-vl-ink-secondary">
                    Owner: {{ $workspace->owner->name }} ({{ $workspace->owner->email }})
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($workspace->status === 'running')
                    <a href="{{ route('workspace.show', $workspace->slug) }}" target="_blank" rel="noopener" class="vl-btn-primary !py-2 !px-4 !text-xs">Open Workspace</a>
                    <form method="POST" action="{{ route('admin.workspaces.stop', $workspace->slug) }}" onsubmit="event.preventDefault(); vcConfirm('Force stop this container?', () => this.submit())">
                        @csrf
                        <button type="submit" class="vl-btn-danger !py-2 !px-4 !text-xs">Force Stop</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('workspace.start', $workspace->slug) }}">
                        @csrf
                        <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs">Start Workspace</button>
                    </form>
                @endif

                @if($workspace->status !== 'archived')
                    <form method="POST" action="{{ route('admin.workspaces.archive', $workspace->slug) }}" onsubmit="event.preventDefault(); vcConfirm('Archive this workspace?', () => this.submit())">
                        @csrf
                        <button type="submit" class="vl-btn-secondary !py-2 !px-4 !text-xs">Archive</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card title="Container Metadata" :padding="true">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-vl-ink-secondary">Course</dt>
                    <dd class="font-medium text-vl-ink">{{ $workspace->course->title ?? 'Standalone' }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-vl-ink-secondary">Created</dt>
                    <dd class="font-mono text-xs text-vl-ink tabular-nums">{{ $workspace->created_at->format('M d, Y · H:i') }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-vl-ink-secondary">Last Heartbeat</dt>
                    <dd class="font-mono text-xs text-vl-ink tabular-nums">{{ $workspace->heartbeat_at ? $workspace->heartbeat_at->format('M d, Y · H:i') : 'Never' }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-vl-ink-secondary">Container Port</dt>
                    <dd class="font-mono text-xs text-vl-accent tabular-nums">{{ $workspace->port ?? 'N/A' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Recent Audit Activity" :padding="true">
            @if($auditLogs->isEmpty())
                <p class="text-sm text-vl-ink-muted py-4">No audit events recorded for this workspace.</p>
            @else
                <ul class="space-y-3 text-sm">
                    @foreach($auditLogs as $log)
                        <li class="flex items-start gap-3">
                            <span class="mt-2 h-1.5 w-1.5 rounded-full bg-vl-accent shrink-0"></span>
                            <div class="min-w-0">
                                <p class="font-mono text-xs font-semibold text-vl-ink">{{ $log->action }}</p>
                                <p class="text-[11px] text-vl-ink-muted">by {{ $log->actor->name ?? 'System' }} &middot; {{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>

    {{-- Per-Workspace Extensions Management --}}
    <x-card title="Per-Workspace Extensions" subtitle="Toggle individual VSIX extensions for this container. Changes synchronize automatically while running." :padding="true">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($allExtensions as $ext)
                @php
                    $pivot = $workspaceExtMap->get($ext->id);
                    $isEnabled = $pivot ? (bool) $pivot->is_enabled : false;
                    $syncStatus = $pivot ? $pivot->sync_status : 'not_assigned';
                @endphp
                <div class="flex items-center justify-between gap-3 rounded-xl bg-vl-surface-raised/70 p-3.5" id="ext-row-{{ $ext->id }}">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-vl-ink truncate">{{ $ext->name }}</p>
                        <p class="font-mono text-[11px] text-vl-ink-muted truncate">{{ $ext->package_identifier }}</p>
                        <span
                            id="sync-badge-{{ $ext->id }}"
                            class="mt-1 inline-block rounded px-1.5 py-0.5 font-mono text-[10px] font-medium {{ $syncStatus === 'synced' ? 'bg-vl-teal/15 text-vl-teal' : ($syncStatus === 'failed' ? 'bg-vl-danger/15 text-vl-danger' : 'bg-vl-gate/15 text-vl-gate') }}"
                        >
                            {{ $syncStatus }}
                        </span>
                    </div>

                    <button
                        type="button"
                        onclick="toggleWorkspaceExt('{{ $workspace->slug }}', {{ $ext->id }}, this)"
                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors cursor-pointer {{ $isEnabled ? 'bg-vl-accent-cta' : 'bg-vl-border/25' }}"
                        data-enabled="{{ $isEnabled ? 'true' : 'false' }}"
                        aria-label="Toggle {{ $ext->name }}"
                    >
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $isEnabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            @endforeach
        </div>

        @if($allExtensions->isEmpty())
            <p class="text-sm text-center py-4 text-vl-ink-muted">No active extensions available in the registry.</p>
        @endif
    </x-card>

    @if($workspace->status === 'running')
        <div
            x-data="{
                stats: {},
                error: false,
                init() {
                    this.fetchStats();
                    setInterval(() => this.fetchStats(), 5000);
                },
                async fetchStats() {
                    try {
                        const res = await fetch('{{ route('workspace.status', $workspace->slug) }}');
                        if (!res.ok) throw new Error();
                        const data = await res.json();
                        if (data.stats) {
                            this.stats = data.stats;
                            this.error = false;
                        } else {
                            this.error = true;
                        }
                    } catch {
                        this.error = true;
                    }
                }
            }"
            class="vl-panel p-6 space-y-4"
        >
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-vl-ink">Live Resource Monitoring</h3>
                <span class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold" :class="error ? 'text-vl-danger' : 'text-vl-teal'">
                    <span class="h-2 w-2 rounded-full" :class="error ? 'bg-vl-danger' : 'bg-vl-teal animate-pulse'"></span>
                    <span x-text="error ? 'Offline' : 'Connected'"></span>
                </span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-xl bg-vl-surface-raised/75 p-4">
                    <p class="text-xs text-vl-ink-muted">CPU Usage</p>
                    <p class="mt-1 font-mono text-xl font-bold text-vl-ink tabular-nums" x-text="stats.CPUPerc || stats.cpu || '0.00%'"></p>
                </div>
                <div class="rounded-xl bg-vl-surface-raised/75 p-4">
                    <p class="text-xs text-vl-ink-muted">Memory Usage</p>
                    <p class="mt-1 font-mono text-xl font-bold text-vl-ink tabular-nums" x-text="stats.MemPerc || stats.memory || '0.00%'"></p>
                    <p class="mt-0.5 font-mono text-[11px] text-vl-ink-muted" x-text="stats.MemUsage || '—'"></p>
                </div>
                <div class="rounded-xl bg-vl-surface-raised/75 p-4">
                    <p class="text-xs text-vl-ink-muted">Network I/O</p>
                    <p class="mt-2 font-mono text-sm font-semibold text-vl-ink tabular-nums" x-text="stats.NetIO || '—'"></p>
                </div>
                <div class="rounded-xl bg-vl-surface-raised/75 p-4">
                    <p class="text-xs text-vl-ink-muted">Block I/O</p>
                    <p class="mt-2 font-mono text-sm font-semibold text-vl-ink tabular-nums" x-text="stats.BlockIO || '—'"></p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
async function toggleWorkspaceExt(workspaceSlug, extensionId, btn) {
    btn.disabled = true;
    btn.style.opacity = '0.5';
    try {
        const res = await fetch(`/admin/workspaces/${workspaceSlug}/extensions/${extensionId}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (data.success) {
            const isEnabled = Boolean(data.is_enabled);
            btn.dataset.enabled = isEnabled ? 'true' : 'false';
            btn.classList.toggle('bg-vl-accent-cta', isEnabled);
            btn.classList.toggle('bg-vl-border/25', !isEnabled);
            const dot = btn.querySelector('span');
            if (dot) {
                dot.classList.toggle('translate-x-6', isEnabled);
                dot.classList.toggle('translate-x-1', !isEnabled);
            }
            const badge = document.getElementById(`sync-badge-${extensionId}`);
            if (badge) {
                badge.textContent = 'pending';
                badge.className = 'mt-1 inline-block rounded px-1.5 py-0.5 font-mono text-[10px] font-medium bg-vl-gate/15 text-vl-gate';
            }
        }
    } finally {
        btn.disabled = false;
        btn.style.opacity = '1';
    }
}
</script>
@endpush
