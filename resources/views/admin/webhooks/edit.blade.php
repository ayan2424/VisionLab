@extends('layouts.admin')
@section('title', 'Edit Webhook')
@section('page-title', 'Edit Webhook')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.webhooks.index') }}" class="text-sm" style="color:var(--vc-muted);">&larr; Back to Webhooks</a>
</div>

<div class="vc-card p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.webhooks.update', $webhook->id) }}" class="space-y-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Payload URL</label>
            <input type="url" name="url" required class="vc-input w-full" value="{{ old('url', $webhook->url) }}">
            @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Events</label>
            <div class="space-y-2 p-4 border rounded-lg bg-black/10 border-gray-700/50">
                @php $events = old('events', $webhook->events ?? []); @endphp
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="events[]" value="workspace.started" {{ in_array('workspace.started', $events) ? 'checked' : '' }} class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
                    <span class="text-sm">workspace.started</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="events[]" value="workspace.stopped" {{ in_array('workspace.stopped', $events) ? 'checked' : '' }} class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
                    <span class="text-sm">workspace.stopped</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="events[]" value="user.registered" {{ in_array('user.registered', $events) ? 'checked' : '' }} class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
                    <span class="text-sm">user.registered</span>
                </label>
            </div>
            @error('events')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Secret (Optional)</label>
            <input type="text" name="secret" class="vc-input w-full" value="{{ old('secret', $webhook->secret) }}">
            <p class="text-xs mt-1" style="color:var(--vc-muted);">Used to generate HMAC-SHA256 signature.</p>
        </div>

        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $webhook->is_active) ? 'checked' : '' }} class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
            <span class="text-sm font-semibold">Active</span>
        </label>

        <button type="submit" class="btn-primary py-2 px-6">Update Webhook</button>
    </form>
</div>
@endsection
