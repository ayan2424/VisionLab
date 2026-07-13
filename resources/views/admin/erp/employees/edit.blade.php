@extends('layouts.admin')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Link User Account</label>
                        <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" disabled>
                            <option value="{{ $employee->user_id }}">{{ $employee->user->name }} ({{ $employee->user->email }})</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">User account cannot be changed after creation.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Department</label>
                        <select name="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select a department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Designation</label>
                        <input type="text" name="designation" value="{{ $employee->designation }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Base Salary ($)</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ $employee->base_salary }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2" style="color:var(--vc-text);">Hire Date</label>
                        <input type="date" name="hire_date" value="{{ $employee->hire_date->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" required>
                    </div>

                    <div class="mb-4 flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $employee->is_active ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm">Active Employee</label>
                    </div>

                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('admin.employees.index') }}" class="mr-4 text-sm text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
