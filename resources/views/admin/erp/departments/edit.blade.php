@extends('layouts.admin')
@section('title', 'Edit Department')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.departments.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Edit Department</h1>
            <p class="text-slate-400 text-sm">Update details for {{ $department->name }}.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Campus</label>
            <select name="campus_id" class="vc-input w-full" required>
                <option value="">Select Campus...</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}" {{ old('campus_id', $department->campus_id) == $campus->id ? 'selected' : '' }}>
                        {{ $campus->name }}
                    </option>
                @endforeach
            </select>
            @error('campus_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Name</label>
                <input type="text" name="name" value="{{ old('name', $department->name) }}" required class="vc-input w-full" placeholder="e.g. Computer Science">
                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Code</label>
                <input type="text" name="code" value="{{ old('code', $department->code) }}" class="vc-input w-full" placeholder="e.g. CS">
                @error('code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Description</label>
            <textarea name="description" rows="3" class="vc-input w-full">{{ old('description', $department->description) }}</textarea>
            @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-gray-800">
                <span class="text-sm font-bold text-slate-300">Is Active</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.departments.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Update Department</button>
        </div>
    </form>
</div>
@endsection
