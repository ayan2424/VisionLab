@extends('layouts.admin')
@section('title', 'Manage Course Batches')

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Course Batches</h1>
            <p class="text-slate-400 text-sm">Assign instructors to course batches and manage scheduling.</p>
        </div>
        <a href="{{ route('admin.batches.create') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white flex items-center gap-2" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Batch
        </a>
    </div>

    <div class="rounded-2xl border border-white/[0.07] overflow-hidden" style="background:#111111;">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-white/[0.02] border-b border-white/[0.07] text-slate-400">
                <tr>
                    <th class="px-6 py-4 font-semibold">Batch Name</th>
                    <th class="px-6 py-4 font-semibold">Course</th>
                    <th class="px-6 py-4 font-semibold">Department</th>
                    <th class="px-6 py-4 font-semibold">Instructor</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.07]">
                @forelse($batches as $batch)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4 font-medium text-white">{{ $batch->title }}</td>
                    <td class="px-6 py-4">{{ $batch->course->title ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $batch->department->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @if($batch->instructor)
                            <div class="flex items-center gap-2">
                                <img src="{{ $batch->instructor->avatar_url }}" alt="Avatar" class="w-6 h-6 rounded-full">
                                <span>{{ $batch->instructor->name }}</span>
                            </div>
                        @else
                            <span class="text-amber-500/80">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full uppercase tracking-wider {{ $batch->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20' }}">
                            {{ $batch->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.batches.edit', $batch->id) }}" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">No course batches found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $batches->links() }}</div>
</div>
@endsection
