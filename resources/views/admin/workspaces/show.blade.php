@extends('layouts.dashboard')

@section('title', $workspace->name . ' — Workspace Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" style="animation:fadeSlideUp .4s ease both;">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.workspaces.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors"
           style="background:var(--vc-card);border:1px solid var(--vc-border);color:var(--vc-muted);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold" style="color:var(--vc-heading);">{{ $workspace->name }}</h1>
            <p class="text-sm mt-0.5 font-mono" style="color:var(--vc-muted);">Slug: {{ $workspace->slug }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Status Card --}}
        <div class="rounded-2xl p-5" style="background:var(--vc-card);border:1px solid var(--vc-border);">
            <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--vc-muted);">Container Status</h3>
            <div class="flex items-center gap-3">
                @if($status['running'])
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-lg font-bold text-emerald-400">Running</span>
                @else
                <span class="w-3 h-3 rounded-full bg-slate-500"></span>
                <span class="text-lg font-bold text-slate-400">Stopped</span>
                @endif
            </div>
            @if($status['port'])
            <p class="text-xs mt-2 font-mono" style="color:var(--vc-muted);">Port: {{ $status['port'] }}</p>
            @endif
            @if($status['container_id'])
            <p class="text-xs mt-1 font-mono truncate" style="color:var(--vc-muted);">ID: {{ substr($status['container_id'], 0, 12) }}</p>
            @endif
        </div>

        {{-- Owner Card --}}
        <div class="rounded-2xl p-5" style="background:var(--vc-card);border:1px solid var(--vc-border);">
            <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--vc-muted);">Owner</h3>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold"
                     style="background:linear-gradient(135deg,rgba(139,92,246,.2),rgba(6,182,212,.2));color:#a78bfa;">
                    {{ $workspace->owner?->avatar_initials ?? '?' }}
                </div>
                <div>
                    <p class="font-semibold" style="color:var(--vc-heading);">{{ $workspace->owner?->name ?? 'Unknown' }}</p>
                    <p class="text-xs" style="color:var(--vc-muted);">{{ $workspace->owner?->email ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Config Card --}}
        <div class="rounded-2xl p-5" style="background:var(--vc-card);border:1px solid var(--vc-border);">
            <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--vc-muted);">Configuration</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span style="color:var(--vc-muted);">Language</span>
                    <span class="font-mono font-semibold" style="color:var(--vc-text);">{{ $workspace->language ?? 'python' }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--vc-muted);">Visibility</span>
                    <span class="font-semibold" style="color:var(--vc-text);">{{ $workspace->is_public ? 'Public' : 'Private' }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--vc-muted);">Max Users</span>
                    <span class="font-semibold" style="color:var(--vc-text);">{{ $workspace->max_participants }}</span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--vc-muted);">Created</span>
                    <span class="font-semibold" style="color:var(--vc-text);">{{ $workspace->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- File Explorer --}}
    <div class="rounded-2xl p-5" style="background:var(--vc-card);border:1px solid var(--vc-border);">
        <h3 class="text-xs font-semibold uppercase tracking-wider mb-4" style="color:var(--vc-muted);">Workspace Files</h3>
        @if(count($files) > 0)
        <div class="space-y-1">
            @foreach($files as $file)
            <div class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/[0.02] transition-colors">
                @if($file['type'] === 'directory')
                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                @else
                <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                @endif
                <span class="text-sm font-mono {{ $file['type'] === 'directory' ? 'font-semibold' : '' }}" style="color:var(--vc-text);">{{ $file['name'] }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-center py-6" style="color:var(--vc-muted);">No files found in workspace directory.</p>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        @if($status['running'])
        <form method="POST" action="{{ route('admin.workspaces.stop', $workspace) }}">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-all bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20"
                    onclick="return confirm('Stop this workspace?')">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Stop Container
                </span>
            </button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.workspaces.destroy', $workspace) }}">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-all bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20"
                    onclick="return confirm('Delete this workspace permanently? This cannot be undone.')">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Workspace
                </span>
            </button>
        </form>
    </div>
</div>
@endsection
