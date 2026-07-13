@extends('layouts.admin')
@section('title', 'Add New Employee')
@section('page-title', 'Add New Employee')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.employees.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Add Employee</h1>
            <p class="text-slate-400 text-sm">Register a new employee and link their account.</p>
        </div>
    </div>

    <form action="{{ route('admin.employees.store') }}" method="POST" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Link User Account</label>
            <select name="user_id" class="vc-input w-full" required>
                <option value="">Select a user...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Department</label>
            <select name="department_id" class="vc-input w-full">
                <option value="">Select a department...</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            @error('department_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Designation</label>
            <input type="text" name="designation" value="{{ old('designation') }}" class="vc-input w-full" required placeholder="e.g. Professor">
            @error('designation') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Base Salary ($)</label>
                <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary') }}" class="vc-input w-full" required placeholder="50000.00">
                @error('base_salary') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Hire Date</label>
                <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="vc-input w-full" required>
                @error('hire_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-gray-800">
                <span class="text-sm font-bold text-slate-300">Active Employee</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.employees.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Save Employee</button>
        </div>
    </form>
</div>
@endsection
