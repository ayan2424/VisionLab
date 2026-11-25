@extends('layouts.dashboard')
@section('title', 'My Workspaces')
@section('page-title', 'Workspaces')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">My Workspaces</h2>
            <p class="text-sm text-vl-ink-secondary tabular-nums">
                Using <strong class="font-semibold text-vl-ink">{{ $workspaces->count() }}</strong> of <strong class="font-semibold text-vl-ink">{{ $quota->max_workspaces }}</strong> provisioned cloud IDE sandboxes.
            </p>
        </div>

        <a href="{{ route('workspace.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm shrink-0">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            New Workspace
        </a>
    </div>

    @if($workspaces->isEmpty())
        <x-card :padding="true">
            <x-empty-state
                title="No workspaces provisioned yet"
                description="Create an isolated Docker & Nix workspace to launch VS Code directly in your browser."
            >
                <a href="{{ route('workspace.create') }}" class="vl-btn-primary !py-2 !px-5 !text-xs">Create Workspace</a>
            </x-empty-state>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($workspaces as $ws)
                <div class="vl-panel p-6 flex flex-col justify-between gap-5">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-vl-ink truncate">{{ $ws->name }}</h3>
                                <p class="font-mono text-[11px] text-vl-ink-muted truncate mt-0.5">{{ $ws->slug }}</p>
                            </div>
                            <x-badge :variant="$ws->status === 'running' ? 'success' : ($ws->status === 'stopped' ? 'neutral' : 'info')" dot>
                                {{ ucfirst($ws->status) }}
                            </x-badge>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge variant="info">{{ strtoupper($ws->language) }}</x-badge>
                            @if($ws->course)
                                <x-badge variant="neutral">{{ $ws->course->title }}</x-badge>
                            @endif
                        </div>

                        <p class="text-xs text-vl-ink-muted">Created {{ $ws->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="pt-4 border-t border-vl-border/10 flex items-center gap-2">
                        <a href="{{ route('workspace.show', $ws->slug) }}" class="vl-btn-primary flex-1 !py-2 !px-3.5 !text-xs">
                            {{ $ws->status === 'running' ? 'Open IDE' : 'Boot Workspace' }}
                        </a>

                        @if($ws->status === 'running')
                            <form method="POST" action="{{ route('workspace.stop', $ws->slug) }}">
                                @csrf
                                <button type="submit" title="Stop Workspace" class="vl-btn-secondary !p-2 text-vl-gate">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('workspace.restart', $ws->slug) }}" title="Restart Workspace" class="vl-btn-secondary !p-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                        </a>

                        <form method="POST" action="{{ route('workspace.destroy', $ws->slug) }}" onsubmit="event.preventDefault(); vcConfirm('Permanently delete this workspace and wipe all files?', () => this.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete Workspace" class="vl-btn-danger !p-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
