@extends('layouts.admin')
@section('title', 'Extension Management')
@section('page-title', 'Extensions')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">SHA256 Extension Registry</h2>
            <p class="text-sm text-vl-ink-secondary">Approve verified VSIX packages and toggle global availability across campus containers.</p>
        </div>
        <a href="{{ route('admin.extensions.create') }}" class="vl-btn-primary !py-2 !px-4 !text-xs sm:!text-sm shrink-0">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Extension
        </a>
    </div>

    <form method="GET" class="vl-panel p-4 flex flex-wrap sm:flex-nowrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or package identifier..." class="vl-input flex-1 !py-2">
        <button type="submit" class="vl-btn-primary !py-2 !px-4 !text-xs">Filter</button>
    </form>

    <x-table>
        <x-slot:head>
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Extension</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Version</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-vl-ink-secondary">Type</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-vl-ink-secondary">Active</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-vl-ink-secondary">Actions</th>
            </tr>
        </x-slot:head>

        @forelse($extensions as $ext)
            <tr class="hover:bg-vl-surface-raised/50 transition-colors duration-150">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-8 w-8 rounded-xl shrink-0 bg-vl-accent/14 text-vl-accent flex items-center justify-center">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-vl-ink truncate">{{ $ext->name }}</p>
                            <p class="font-mono text-xs text-vl-ink-muted truncate">{{ $ext->package_identifier }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 font-mono text-xs text-vl-ink-secondary">v{{ $ext->version }}</td>
                <td class="px-5 py-3.5">
                    <x-badge :variant="$ext->is_builtin ? 'info' : 'neutral'">
                        {{ $ext->is_builtin ? 'Built-in' : 'Custom' }}
                    </x-badge>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <button
                        type="button"
                        onclick="toggleExtension({{ $ext->id }}, this)"
                        data-active="{{ $ext->is_active ? '1' : '0' }}"
                        class="relative inline-flex items-center w-10 h-5 rounded-full transition-colors cursor-pointer {{ $ext->is_active ? 'bg-vl-accent-cta' : 'bg-vl-border/25' }}"
                        aria-label="Toggle {{ $ext->name }}"
                    >
                        <span class="inline-block w-4 h-4 rounded-full bg-white shadow transition-transform {{ $ext->is_active ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                    </button>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="inline-flex items-center justify-end gap-2">
                        <a href="{{ route('admin.extensions.edit', $ext->id) }}" class="vl-btn-secondary !py-1 !px-3 !text-xs">Edit</a>
                        @if(!$ext->is_builtin)
                            <form method="POST" action="{{ route('admin.extensions.destroy', $ext->id) }}" onsubmit="event.preventDefault(); vcConfirm('Delete this extension from the registry?', () => this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="vl-btn-danger !py-1 !px-3 !text-xs">Delete</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-5 py-10 text-center text-sm text-vl-ink-muted">No extensions registered yet.</td>
            </tr>
        @endforelse
    </x-table>

    <div>{{ $extensions->withQueryString()->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function toggleExtension(id, btn) {
    fetch(`/admin/extensions/${id}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then((r) => r.json())
    .then((data) => {
        if (!data.success) return;
        const nowActive = Boolean(data.is_active);
        btn.dataset.active = nowActive ? '1' : '0';
        btn.classList.toggle('bg-vl-accent-cta', nowActive);
        btn.classList.toggle('bg-vl-border/25', !nowActive);
        const knob = btn.querySelector('span');
        if (knob) {
            knob.classList.toggle('translate-x-5', nowActive);
            knob.classList.toggle('translate-x-0.5', !nowActive);
        }
    })
    .catch(() => {
        if (window.vcToast) window.vcToast('Failed to toggle extension.', 'error');
    });
}
</script>
@endpush
