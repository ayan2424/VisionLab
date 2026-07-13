@extends('layouts.admin')

@section('title', 'Employees (HR)')
@section('page-title', 'Employees (HR)')

@section('content')
<div class="vc-card overflow-hidden p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold mb-4" style="color:var(--vc-text);">All Employees</h1>
                    <a href="{{ route('admin.employees.create') }}" class="btn-primary py-2 px-4 text-sm">Add Employee</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Name</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Designation</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Department</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Salary</th>
                                <th class="px-5 py-3 text-xs font-semibold" style="color:var(--vc-muted); border-bottom:1px solid var(--vc-border); background:rgba(0,0,0,0.02);">Status</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse ($employees as $employee)
                                <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors border-b" style="border-color:var(--vc-border);">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-medium ">{{ $employee->user->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ $employee->designation }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">{{ $employee->department->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">${{ number_format($employee->base_salary, 2) }}</td>
                                    <td class="px-5 py-4 text-sm" style="color:var(--vc-muted);">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $employee->is_active ? 'green' : 'red' }}-100 text-{{ $employee->is_active ? 'green' : 'red' }}-800">
                                            {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No employees found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
@endsection
