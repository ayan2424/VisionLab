@extends('layouts.admin')
@section('title', 'Manage Workspaces')
@section('page-title', 'Workspaces')

@section('content')
<div class="space-y-6">
    <div class="space-y-1">
        <h2 class="text-2xl font-bold tracking-tight text-vl-ink">Student &amp; Faculty Workspaces</h2>
        <p class="text-sm text-vl-ink-secondary">Inspect container states, stop runaway sandboxes, and manage workspace lifecycle across campus.</p>
    </div>

    <x-table>
        <x-slot:head>
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Workspace</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Course</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Owner</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Status</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-vl-ink-secondary">Actions</th>
            </tr>
        </x-slot:head>

        @forelse($workspaces as $ws)
            <tr class="hover:bg-vl-surface-raised/50 transition-colors duration-150">
                <td class="px-5 py-3.5">
                    <p class="font-mono text-xs sm:text-sm font-semibold text-vl-ink">{{ $ws->slug }}</p>
                    <p class="text-[11px] text-vl-ink-muted">Created {{ $ws->created_at->diffForHumans() }}</p>
                </td>
                <td class="px-5 py-3.5 text-sm">
                    @if($ws->course)
                        <a href="{{ route('courses.show', $ws->course->slug) }}" class="font-medium text-vl-accent hover:underline">
                            {{ $ws->course->title }}
                        </a>
                    @else
                        <span class="text-vl-ink-muted">&mdash;</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2.5">
                        <span class="h-7 w-7 rounded-lg bg-vl-accent/14 text-vl-accent text-[11px] font-bold flex items-center justify-center shrink-0">
                            {{ $ws->owner->avatar_initials }}
                        </span>
                        <span class="text-sm text-vl-ink-secondary truncate">{{ $ws->owner->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <x-badge :variant="$ws->status === 'running' ? 'success' : ($ws->status === 'stopped' ? 'neutral' : 'info')" dot>
                        {{ ucfirst($ws->status) }}
                    </x-badge>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center justify-end gap-2">
                        <a href="{{ route('admin.workspaces.show', $ws->slug) }}" class="vl-btn-secondary !py-1 !px-3 !text-xs">Inspect</a>
                        @if($ws->status === 'running')
                            <form method="POST" action="{{ route('admin.workspaces.stop', $ws->slug) }}" class="inline" onsubmit="event.preventDefault(); vcConfirm('Force stop this container?', () => this.submit())">
                                @csrf
                                <button type="submit" class="rounded-xl px-2.5 py-1 text-xs font-semibold text-vl-gate bg-vl-gate/12 hover:bg-vl-gate/22 transition-colors cursor-pointer">Stop</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.workspaces.destroy', $ws->slug) }}" class="inline" onsubmit="event.preventDefault(); vcConfirm('Permanently delete this workspace and wipe all container files?', () => this.submit())">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="vl-btn-danger !py-1 !px-3 !text-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-5 py-10 text-center text-sm text-vl-ink-muted">No workspaces provisioned yet.</td>
            </tr>
        @endforelse
    </x-table>

    @if($workspaces->hasPages())
        <div>{{ $workspaces->links() }}</div>
    @endif
</div>
@endsection
