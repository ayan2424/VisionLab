<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'department'])->latest()->get();
        return view('admin.erp.employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['instructor', 'admin'])->get();
        $departments = Department::all();
        return view('admin.erp.employees.create', compact('users', 'departments'));
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
        return view('admin.erp.employees.edit', compact('employee', 'users', 'departments'));
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
