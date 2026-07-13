@extends('layouts.admin')
@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.employees.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.05] transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Edit Employee</h1>
            <p class="text-slate-400 text-sm">Update records for {{ $employee->user->name }}.</p>
        </div>
    </div>

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" class="vc-card p-6 border-t-4" style="border-top-color:var(--vc-accent);">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">User Account</label>
            <select name="user_id" class="vc-input w-full" disabled>
                <option value="{{ $employee->user_id }}">{{ $employee->user->name }} ({{ $employee->user->email }})</option>
            </select>
            <p class="text-xs text-slate-500 mt-1">User account cannot be changed after creation.</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Department</label>
            <select name="department_id" class="vc-input w-full">
                <option value="">Select a department...</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-300">Designation</label>
            <input type="text" name="designation" value="{{ $employee->designation }}" class="vc-input w-full" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Base Salary ($)</label>
                <input type="number" step="0.01" name="base_salary" value="{{ $employee->base_salary }}" class="vc-input w-full" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-300">Hire Date</label>
                <input type="date" name="hire_date" value="{{ $employee->hire_date->format('Y-m-d') }}" class="vc-input w-full" required>
            </div>
        </div>

        <div class="mb-8">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="w-5 h-5 rounded border-gray-600 text-indigo-600 focus:ring-indigo-500 bg-gray-800" {{ $employee->is_active ? 'checked' : '' }}>
                <span class="text-sm font-bold text-slate-300">Active Employee</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.employees.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] transition-colors">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg">Update Employee</button>
        </div>
    </form>
</div>
@endsection
