@extends('layouts.dashboard')
@section('title', 'My Workspaces')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--vc-text);">My Workspaces</h1>
            <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">
                Manage your cloud IDE environments. Using {{ $workspaces->count() }} of {{ $quota->max_workspaces }} allowed workspaces.
            </p>
        </div>
        <a href="{{ route('workspace.create') }}" class="btn-primary py-2 px-4 rounded-lg font-bold text-sm shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Workspace
        </a>
    </div>

    @if($workspaces->isEmpty())
        <div class="vc-card text-center py-16">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-xl font-bold mb-2" style="color:var(--vc-text);">No Workspaces Found</h3>
            <p class="text-sm mb-6" style="color:var(--vc-text-secondary);">You haven't created any workspaces yet. Create your first environment to get started.</p>
            <a href="{{ route('workspace.create') }}" class="btn-primary py-2 px-6 rounded-lg font-bold">Create Workspace</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($workspaces as $ws)
            <div class="vc-card flex flex-col transition-all hover:border-[var(--vc-accent)]/30">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-lg" style="color:var(--vc-text);">{{ $ws->name }}</h3>
                        <p class="text-xs mt-1" style="color:var(--vc-muted);">Created {{ $ws->created_at->diffForHumans() }}</p>
                    </div>
                    @if($ws->status === 'running')
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Running
                        </span>
                    @elseif($ws->status === 'stopped')
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-500/10 text-gray-400">Stopped</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-500">{{ ucfirst($ws->status) }}</span>
                    @endif
                </div>

                <div class="mb-6 flex items-center gap-2">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--vc-surface-dark);color:var(--vc-accent);">
                        {{ $ws->language }}
                    </span>
                    @if($ws->course)
                        <span class="px-2 py-1 rounded text-[10px] font-bold tracking-wider" style="background:var(--vc-surface-dark);color:var(--vc-text-secondary);">
                            {{ $ws->course->title }}
                        </span>
                    @endif
                </div>

                <div class="mt-auto flex items-center gap-2 pt-4 border-t" style="border-color:var(--vc-border);">
                    @if($ws->status === 'running')
                        <a href="{{ route('workspace.show', $ws->slug) }}" class="btn-primary py-1.5 px-4 rounded text-xs font-bold flex-1 text-center">Open IDE</a>
                        <form method="POST" action="{{ route('workspace.stop', $ws->slug) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-ghost py-1.5 px-3 rounded text-xs text-yellow-500" title="Stop Workspace">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('workspace.show', $ws->slug) }}" class="btn-primary py-1.5 px-4 rounded text-xs font-bold flex-1 text-center flex items-center justify-center gap-1 bg-opacity-80">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Boot Workspace
                        </a>
                    @endif
                    <a href="{{ route('workspace.rebuild', $ws->slug) }}" class="btn-ghost py-1.5 px-3 rounded text-xs text-blue-400 hover:bg-blue-400/10" title="Restart Workspace">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </a>
                    <form method="POST" action="{{ route('workspace.destroy', $ws->slug) }}" class="inline" onsubmit="event.preventDefault(); vcConfirm('WARNING: This will permanently delete your workspace and all its data. Continue?', () => this.submit())">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost py-1.5 px-3 rounded text-xs text-red-500 hover:bg-red-500/10" title="Delete Workspace">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
