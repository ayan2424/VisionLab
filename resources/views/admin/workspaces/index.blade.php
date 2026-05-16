@extends('layouts.dashboard')

@section('title', 'Workspace Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" style="animation:fadeSlideUp .4s ease both;">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold" style="color:var(--vc-heading);">Workspace Management</h1>
            <p class="text-sm mt-1" style="color:var(--vc-muted);">Manage all code-server containers and workspaces</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold"
                  style="background:var(--vc-card);border:1px solid var(--vc-border);color:var(--vc-muted);">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ $workspaces->total() }} total workspaces
            </span>
        </div>
    </div>

    {{-- Workspace Table --}}
    <div class="rounded-2xl overflow-hidden" style="background:var(--vc-card);border:1px solid var(--vc-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--vc-border);">
                    <th class="text-left px-5 py-4 font-semibold text-xs uppercase tracking-wider" style="color:var(--vc-muted);">Workspace</th>
                    <th class="text-left px-5 py-4 font-semibold text-xs uppercase tracking-wider" style="color:var(--vc-muted);">Owner</th>
                    <th class="text-left px-5 py-4 font-semibold text-xs uppercase tracking-wider" style="color:var(--vc-muted);">Language</th>
                    <th class="text-left px-5 py-4 font-semibold text-xs uppercase tracking-wider" style="color:var(--vc-muted);">Status</th>
                    <th class="text-left px-5 py-4 font-semibold text-xs uppercase tracking-wider" style="color:var(--vc-muted);">Port</th>
                    <th class="text-right px-5 py-4 font-semibold text-xs uppercase tracking-wider" style="color:var(--vc-muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workspaces as $ws)
                @php $st = $statuses[$ws->id] ?? ['running' => false, 'port' => null]; @endphp
                <tr class="hover:bg-white/[0.02] transition-colors" style="border-bottom:1px solid var(--vc-border);">
                    {{-- Name --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold"
                                 style="background:linear-gradient(135deg,rgba(139,92,246,.2),rgba(6,182,212,.2));color:#a78bfa;">
                                {{ strtoupper(substr($ws->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold" style="color:var(--vc-heading);">{{ $ws->name }}</p>
                                <p class="text-xs font-mono" style="color:var(--vc-muted);">{{ $ws->slug }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Owner --}}
                    <td class="px-5 py-4" style="color:var(--vc-text);">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold"
                                 style="background:rgba(139,92,246,.15);color:#a78bfa;">
                                {{ $ws->owner?->avatar_initials ?? '?' }}
                            </div>
                            {{ $ws->owner?->name ?? 'Unknown' }}
                        </div>
                    </td>

                    {{-- Language --}}
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-md text-xs font-mono font-semibold"
                              style="background:rgba(6,182,212,.1);color:#22d3ee;border:1px solid rgba(6,182,212,.2);">
                            {{ $ws->language ?? 'python' }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-4">
                        @if($st['running'])
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Running
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                            Stopped
                        </span>
                        @endif
                    </td>

                    {{-- Port --}}
                    <td class="px-5 py-4 font-mono text-xs" style="color:var(--vc-muted);">
                        {{ $st['port'] ?? '—' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.workspaces.show', $ws) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                               style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);">
                                View
                            </a>
                            @if($st['running'])
                            <form method="POST" action="{{ route('admin.workspaces.stop', $ws) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20"
                                        onclick="return confirm('Stop this workspace container?')">
                                    Stop
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.workspaces.destroy', $ws) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20"
                                        onclick="return confirm('Delete this workspace permanently?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center" style="color:var(--vc-muted);">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <p class="font-semibold text-sm">No workspaces yet</p>
                        <p class="text-xs mt-1">Workspaces are created when users open the IDE.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($workspaces->hasPages())
        <div class="px-5 py-4" style="border-top:1px solid var(--vc-border);">
            {{ $workspaces->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
