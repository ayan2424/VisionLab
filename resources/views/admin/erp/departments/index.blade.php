@extends('layouts.admin')
@section('title', 'Manage Departments')

@section('content')
<div class="max-w-5xl">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-vl-ink mb-1">Departments</h1>
            <p class="text-vl-ink-secondary text-sm">Manage academic departments within campuses.</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="vl-btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Department
        </a>
    </div>

    <div class="vl-panel overflow-hidden">
        <table class="w-full text-left text-sm text-vl-ink-secondary">
            <thead class="bg-vl-surface-raised/65 border-b border-vl-border/10 text-vl-ink-secondary">
                <tr>
                    <th class="px-6 py-4 font-semibold">Name</th>
                    <th class="px-6 py-4 font-semibold">Campus</th>
                    <th class="px-6 py-4 font-semibold">Code</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-vl-border/10">
                @forelse($departments as $department)
                <tr class="hover:bg-vl-surface-raised/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-vl-ink">{{ $department->name }}</td>
                    <td class="px-6 py-4">{{ $department->campus->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $department->code ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase tracking-wider {{ $department->is_active ? 'bg-vl-teal/15 text-vl-teal border border-vl-teal/25' : 'bg-slate-500/10 text-vl-ink-secondary border border-slate-500/20' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.departments.edit', $department->id) }}" class="p-2 rounded-lg text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised transition-colors">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-vl-ink-muted">No departments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $departments->links() }}</div>
</div>
@endsection
