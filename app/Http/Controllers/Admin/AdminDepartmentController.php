<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Campus;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('campus')->latest()->paginate(20);
        return view('admin.erp.departments.index', compact('departments'));
    }

    public function create()
    {
        $campuses = Campus::where('is_active', true)->get();
        return view('admin.erp.departments.create', compact('campuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'head_of_dept_id' => 'nullable|exists:users,id',
        ]);
        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);
        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $campuses = Campus::where('is_active', true)->get();
        return view('admin.erp.departments.edit', compact('department', 'campuses'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'head_of_dept_id' => 'nullable|exists:users,id',
        ]);
        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);
        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->batches()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete department because it has active batches.']);
        }
        
        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }
}
