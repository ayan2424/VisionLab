@extends('layouts.admin')
@section('title', 'Manage Campuses')

@section('content')
<div class="max-w-5xl">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-vl-ink mb-1">Campuses</h1>
            <p class="text-vl-ink-secondary text-sm">Manage physical institute campuses.</p>
        </div>
        <a href="{{ route('admin.campuses.create') }}" class="vl-btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Campus
        </a>
    </div>

    <div class="vl-panel overflow-hidden">
        <table class="w-full text-left text-sm text-vl-ink-secondary">
            <thead class="bg-vl-surface-raised/65 border-b border-vl-border/10 text-vl-ink-secondary">
                <tr>
                    <th class="px-6 py-4 font-semibold">Name</th>
                    <th class="px-6 py-4 font-semibold">Location</th>
                    <th class="px-6 py-4 font-semibold">Contact</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-vl-border/10">
                @forelse($campuses as $campus)
                <tr class="hover:bg-vl-surface-raised/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-vl-ink">{{ $campus->name }}</td>
                    <td class="px-6 py-4">{{ $campus->city ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $campus->email ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase tracking-wider {{ $campus->is_active ? 'bg-vl-teal/15 text-vl-teal border border-vl-teal/25' : 'bg-slate-500/10 text-vl-ink-secondary border border-slate-500/20' }}">
                            {{ $campus->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.campuses.edit', $campus->id) }}" class="p-2 rounded-lg text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised transition-colors">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-vl-ink-muted">No campuses found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $campuses->links() }}</div>
</div>
@endsection
