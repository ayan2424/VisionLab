@extends('layouts.admin')
@section('title', 'Create Webhook')
@section('page-title', 'Create Webhook')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.webhooks.index') }}" class="text-sm" style="color:var(--vc-muted);">&larr; Back to Webhooks</a>
</div>

<div class="vc-card p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.webhooks.store') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Payload URL</label>
            <input type="url" name="url" required placeholder="https://example.com/webhook" class="vc-input w-full" value="{{ old('url') }}">
            @error('url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Events</label>
            <div class="space-y-2 p-4 border rounded-lg bg-black/10 border-gray-700/50">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="events[]" value="workspace.started" class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
                    <span class="text-sm">workspace.started</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="events[]" value="workspace.stopped" class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
                    <span class="text-sm">workspace.stopped</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="events[]" value="user.registered" class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
                    <span class="text-sm">user.registered</span>
                </label>
            </div>
            @error('events')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold mb-2" style="color:var(--vc-text);">Secret (Optional)</label>
            <input type="text" name="secret" class="vc-input w-full" value="{{ old('secret') }}">
            <p class="text-xs mt-1" style="color:var(--vc-muted);">Used to generate HMAC-SHA256 signature.</p>
        </div>

        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-600 bg-black/20 text-[#3b82f6]">
            <span class="text-sm font-semibold">Active</span>
        </label>

        <button type="submit" class="btn-primary py-2 px-6">Create Webhook</button>
    </form>
</div>
@endsection
