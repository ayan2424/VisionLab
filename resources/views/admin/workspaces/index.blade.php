@extends('layouts.admin')
@section('title', 'Manage Workspaces')
@section('page-title', 'Workspaces')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Workspaces</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Monitor and manage student environments</p>
    </div>
</div>

<div class="vc-card overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead style="background:var(--vc-surface);border-bottom:1px solid var(--vc-border);">
            <tr>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Workspace</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Course</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Owner</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Status</th>
                <th class="px-4 py-3 font-semibold text-right" style="color:var(--vc-text-secondary);">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y" style="divide-color:var(--vc-border);">
            @forelse($workspaces as $ws)
            <tr class="hover:bg-opacity-5 transition-colors" style="hover:background:var(--vc-border);">
                <td class="px-4 py-3">
                    <div class="font-medium" style="color:var(--vc-text);">{{ $ws->slug }}</div>
                    <div class="text-[11px]" style="color:var(--vc-muted);">Created {{ $ws->created_at->diffForHumans() }}</div>
                </td>
                <td class="px-4 py-3">
                    @if($ws->course)
                        <a href="{{ route('courses.show', $ws->course->slug) }}" class="hover:underline" style="color:var(--vc-accent);">
                            {{ $ws->course->title }}
                        </a>
                    @else
                        <span style="color:var(--vc-muted);">-</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white bg-indigo-500">
                            {{ $ws->owner->avatar_initials }}
                        </div>
                        <span style="color:var(--vc-text-secondary);">{{ $ws->owner->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    @if($ws->status === 'running')
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500">Running</span>
                    @elseif($ws->status === 'stopped')
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-500/10 text-gray-400">Stopped</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-500">{{ ucfirst($ws->status) }}</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.workspaces.show', $ws->slug) }}" class="btn-ghost py-1 px-3 text-xs mr-2">Inspect</a>
                    @if($ws->status === 'running')
                        <form method="POST" action="{{ route('admin.workspaces.stop', $ws->slug) }}" class="inline" onsubmit="return confirm('Force stop this workspace? Data not pushed may be lost.');">
                            @csrf
                            <button class="btn-ghost text-yellow-500 py-1 px-3 text-xs">Stop</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.workspaces.destroy', $ws->slug) }}" class="inline" onsubmit="return confirm('WARNING: This will permanently delete the workspace and all its local physical files. This cannot be undone. Continue?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn-ghost text-red-500 py-1 px-3 text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm" style="color:var(--vc-muted);">No workspaces found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($workspaces->hasPages())
    <div class="px-4 py-3 border-t" style="border-color:var(--vc-border);">
        {{ $workspaces->links() }}
    </div>
    @endif
</div>
@endsection


