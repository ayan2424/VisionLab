@extends('layouts.admin')
@section('title', 'Webhooks Management')
@section('page-title', 'Webhooks')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold" style="color:var(--vc-text);">Webhooks</h1>
    <a href="{{ route('admin.webhooks.create') }}" class="btn-primary py-2 px-4">Create Webhook</a>
</div>

<div class="vc-card overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr style="border-bottom:1px solid var(--vc-border);background:rgba(0,0,0,0.02);">
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Endpoint URL</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Events</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Status</th>
                <th class="px-5 py-3 text-xs font-semibold text-right" style="color:var(--vc-muted);">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($webhooks as $webhook)
            <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors border-b" style="border-color:var(--vc-border);">
                <td class="px-5 py-4">
                    <div class="text-sm font-semibold truncate" style="color:var(--vc-text);">{{ $webhook->url }}</div>
                </td>
                <td class="px-5 py-4">
                    <div class="flex flex-wrap gap-1">
                        @foreach($webhook->events as $event)
                        <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/10 text-blue-500 border border-blue-500/20 uppercase">{{ $event }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="px-5 py-4">
                    <span class="px-2 py-0.5 rounded text-xs border" style="{{ $webhook->is_active ? 'color:#10B981;background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.2)' : 'color:#ef4444;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.2)' }}">
                        {{ $webhook->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.webhooks.edit', $webhook->id) }}" class="btn-ghost py-1 px-3 text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.webhooks.destroy', $webhook->id) }}" onsubmit="return confirm('Are you sure you want to delete this webhook?');">
                            @csrf @method('DELETE')
                            <button class="btn-ghost py-1 px-3 text-xs text-red-500">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-gray-500">No webhooks configured.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
