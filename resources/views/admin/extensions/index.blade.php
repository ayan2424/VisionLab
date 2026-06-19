@extends('layouts.admin')
@section('title', 'Extension Management')
@section('page-title', 'Extensions')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6" style="opacity:0;animation:fadeSlideUp .5s .05s ease forwards">
    <h1 class="text-xl font-bold" style="color:var(--vc-text);">Extension Management</h1>
    <a href="{{ route('admin.extensions.create') }}" class="btn-primary py-2 px-4 text-sm">
        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Extension
    </a>
</div>

<form method="GET" class="flex gap-3 mb-6" style="opacity:0;animation:fadeSlideUp .5s .1s ease forwards">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search extensions…"
           class="vc-input flex-1">
    <button type="submit" class="btn-primary py-2.5 px-5 text-sm">Filter</button>
</form>

<div class="vc-card overflow-x-auto" style="opacity:0;animation:fadeSlideUp .5s .15s ease forwards">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr style="border-bottom:1px solid var(--vc-border);background:rgba(0,0,0,0.02);" class="dark:bg-[#0d1117]">
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Extension</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Version</th>
                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted);">Type</th>
                <th class="px-5 py-3 text-xs font-semibold text-center" style="color:var(--vc-muted);">Global</th>
                <th class="px-5 py-3 text-xs font-semibold text-right" style="color:var(--vc-muted);">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($extensions as $ext)
            <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5" style="border-bottom:1px solid var(--vc-border);">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(240,80,0,0.1);">
                            <svg class="w-4 h-4" style="color:var(--vc-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold truncate" style="color:var(--vc-text);">{{ $ext->name }}</div>
                            <div class="text-xs font-mono truncate" style="color:var(--vc-text-secondary);">{{ $ext->package_identifier }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-xs font-mono" style="color:var(--vc-text-secondary);">v{{ $ext->version }}</td>
                <td class="px-5 py-4">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold border"
                          style="{{ $ext->is_builtin ? 'color:var(--vc-accent);background:rgba(240,80,0,0.1);border-color:rgba(240,80,0,0.2)' : 'color:var(--vc-text-secondary);background:var(--vc-surface);border-color:var(--vc-border)' }}">
                        {{ $ext->is_builtin ? 'Built-in' : 'Custom' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <button type="button"
                            onclick="toggleExtension({{ $ext->id }}, this)"
                            data-active="{{ $ext->is_active ? '1' : '0' }}"
                            class="relative inline-flex items-center w-10 h-5 rounded-full transition-all"
                            style="background:{{ $ext->is_active ? 'var(--vc-accent)' : 'var(--vc-border)' }}">
                        <span class="inline-block w-4 h-4 rounded-full bg-white transition-transform shadow {{ $ext->is_active ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                    </button>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.extensions.edit', $ext->id) }}" class="btn-ghost py-1 px-3 text-xs">Edit</a>
                        @if(!$ext->is_builtin)
                        <form method="POST" action="{{ route('admin.extensions.destroy', $ext->id) }}" onsubmit="return confirm('Delete this extension?')">
                            @csrf @method('DELETE')
                            <button class="btn-ghost py-1 px-3 text-xs" style="color:#EF4444;border-color:rgba(239,68,68,0.3);" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-sm" style="color:var(--vc-muted);">No extensions found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $extensions->withQueryString()->links() }}</div>

<script>
function toggleExtension(id, btn) {
    const isActive = btn.dataset.active === '1';
    fetch(`/admin/extensions/${id}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const nowActive = data.is_active;
            btn.dataset.active = nowActive ? '1' : '0';
            btn.style.background = nowActive ? 'var(--vc-accent)' : 'var(--vc-border)';
            const knob = btn.querySelector('span');
            knob.className = `inline-block w-4 h-4 rounded-full bg-white transition-transform shadow ${nowActive ? 'translate-x-5' : 'translate-x-0.5'}`;
        }
    })
    .catch(() => alert('Failed to update extension.'));
}
</script>
@endsection


