@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('page-title', 'System Audit Trail')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">Audit Logs</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">Immutable record of administrative and system actions</p>
    </div>
</div>

<div class="vc-card overflow-hidden">
    {{-- Filters --}}
    <div class="p-4 border-b flex gap-4" style="border-color:var(--vc-border);background:var(--vc-surface);">
        <form method="GET" action="{{ route('admin.audits.index') }}" class="flex gap-4 w-full">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter by action..." class="text-sm rounded-lg flex-1" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);">
            <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="Filter by User ID..." class="text-sm rounded-lg w-48" style="background:var(--vc-bg);border:1px solid var(--vc-border);color:var(--vc-text);">
            <button class="btn-primary py-2">Filter</button>
            <a href="{{ route('admin.audits.index') }}" class="btn-ghost py-2">Clear</a>
        </form>
    </div>

    <table class="w-full text-left text-sm">
        <thead style="background:var(--vc-surface);border-bottom:1px solid var(--vc-border);">
            <tr>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Timestamp</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Actor</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Action</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Resource</th>
                <th class="px-4 py-3 font-semibold" style="color:var(--vc-text-secondary);">Details</th>
            </tr>
        </thead>
        <tbody class="divide-y" style="divide-color:var(--vc-border);">
            @forelse($audits as $log)
            <tr class="hover:bg-opacity-5 transition-colors" style="hover:background:var(--vc-border);">
                <td class="px-4 py-3 whitespace-nowrap text-xs font-mono" style="color:var(--vc-muted);">
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                </td>
                <td class="px-4 py-3">
                    @if($log->actor)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white bg-indigo-500">
                                {{ $log->actor->avatar_initials }}
                            </div>
                            <span style="color:var(--vc-text-secondary);">{{ $log->actor->name }}</span>
                        </div>
                    @else
                        <span style="color:var(--vc-muted);">System</span>
                    @endif
                </td>
                <td class="px-4 py-3 font-semibold" style="color:var(--vc-text);">
                    {{ $log->action }}
                </td>
                <td class="px-4 py-3 text-xs" style="color:var(--vc-text-secondary);">
                    @if($log->resource_type)
                        {{ class_basename($log->resource_type) }} #{{ $log->resource_id }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-4 py-3 text-[11px] font-mono" style="color:var(--vc-muted);">
                    @if($log->details)
                        <div class="max-w-xs truncate" title="{{ json_encode($log->details) }}">
                            {{ json_encode($log->details) }}
                        </div>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm" style="color:var(--vc-muted);">No audit logs found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($audits->hasPages())
    <div class="px-4 py-3 border-t" style="border-color:var(--vc-border);">
        {{ $audits->links() }}
    </div>
    @endif
</div>
@endsection


