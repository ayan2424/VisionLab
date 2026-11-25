<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'department'])->latest()->get();
        $rows = $employees->map(fn ($e) => [
            'id' => $e->id, 'name' => $e->user?->name ?? '—', 'designation' => $e->designation,
            'dept' => $e->department?->name ?? '—', 'base_salary' => number_format((float) $e->base_salary),
            'is_active' => (bool) $e->is_active,
        ])->values()->all();
        return Inertia::render('Admin/Erp/ResourceIndex', [
            'title' => 'Staff & Faculty Directory',
            'subtitle' => 'Manage institutional HR records, designations, and payroll baselines.',
            'basePath' => '/admin/employees',
            'createLabel' => 'Add Employee',
            'columns' => [
                ['key' => 'name', 'label' => 'Employee'],
                ['key' => 'designation', 'label' => 'Designation'],
                ['key' => 'dept', 'label' => 'Department'],
                ['key' => 'base_salary', 'label' => 'Base Salary', 'mono' => true],
                ['key' => 'is_active', 'label' => 'Status', 'badge' => true],
            ],
            'rows' => $rows,
        ]);
    }

    public function create()
    {
        $users = User::whereIn('role', ['instructor', 'admin'])->get();
        $departments = Department::all();
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Add Employee Record',
            'subtitle' => 'Link a faculty or admin account to HR payroll.',
            'submitUrl' => '/admin/employees',
            'method' => 'post',
            'cancelUrl' => '/admin/employees',
            'fields' => [
                ['name' => 'user_id', 'label' => 'Account', 'type' => 'select', 'required' => true,
                 'options' => $users->map(fn ($u) => ['value' => $u->id, 'label' => "{$u->name} ({$u->email})"])->values()->all()],
                ['name' => 'department_id', 'label' => 'Department', 'type' => 'select',
                 'options' => $departments->map(fn ($d) => ['value' => $d->id, 'label' => $d->name])->values()->all()],
                ['name' => 'designation', 'label' => 'Designation', 'type' => 'text', 'required' => true],
                ['name' => 'base_salary', 'label' => 'Base Salary', 'type' => 'number', 'required' => true],
                ['name' => 'hire_date', 'label' => 'Hire Date', 'type' => 'date', 'required' => true],
                ['name' => 'is_active', 'label' => 'Active Employee', 'type' => 'checkbox'],
            ],
            'initialValues' => ['user_id' => '', 'department_id' => '', 'designation' => '', 'base_salary' => 0, 'hire_date' => '', 'is_active' => true],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:employees,user_id',
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'required|string|max:255',
            'base_salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        Employee::create($validated);
        return redirect()->route('admin.employees.index')->with('success', 'Employee record created successfully.');
    }

    public function edit(Employee $employee)
    {
        $users = User::whereIn('role', ['instructor', 'admin'])->get();
        $departments = Department::all();
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Edit Employee Record',
            'subtitle' => 'Update HR designation and department link.',
            'submitUrl' => "/admin/employees/{$employee->id}",
            'method' => 'put',
            'cancelUrl' => '/admin/employees',
            'fields' => [
                ['name' => 'department_id', 'label' => 'Department', 'type' => 'select',
                 'options' => $departments->map(fn ($d) => ['value' => $d->id, 'label' => $d->name])->values()->all()],
                ['name' => 'designation', 'label' => 'Designation', 'type' => 'text', 'required' => true],
                ['name' => 'base_salary', 'label' => 'Base Salary', 'type' => 'number', 'required' => true],
                ['name' => 'hire_date', 'label' => 'Hire Date', 'type' => 'date', 'required' => true],
                ['name' => 'is_active', 'label' => 'Active Employee', 'type' => 'checkbox'],
            ],
            'initialValues' => [
                'department_id' => $employee->department_id ?? '', 'designation' => $employee->designation,
                'base_salary' => $employee->base_salary,
                'hire_date' => $employee->hire_date ? substr((string) $employee->hire_date, 0, 10) : '',
                'is_active' => (bool) $employee->is_active,
            ],
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'required|string|max:255',
            'base_salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $employee->update($validated);
        return redirect()->route('admin.employees.index')->with('success', 'Employee record updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee record deleted successfully.');
    }
}
