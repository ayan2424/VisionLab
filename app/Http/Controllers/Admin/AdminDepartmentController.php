<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Campus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('campus')->latest()->paginate(20);
        $rows = collect($departments->items())->map(fn ($d) => [
            'id' => $d->id, 'name' => $d->name, 'code' => $d->code,
            'campus_name' => $d->campus?->name ?? '—', 'is_active' => (bool) $d->is_active,
        ])->values()->all();
        return Inertia::render('Admin/Erp/ResourceIndex', [
            'title' => 'Academic Departments',
            'subtitle' => 'Organize faculties and academic divisions per campus.',
            'basePath' => '/admin/departments',
            'createLabel' => 'Add Department',
            'columns' => [
                ['key' => 'name', 'label' => 'Department'],
                ['key' => 'code', 'label' => 'Code', 'mono' => true],
                ['key' => 'campus_name', 'label' => 'Campus'],
                ['key' => 'is_active', 'label' => 'Status', 'badge' => true],
            ],
            'rows' => $rows,
            'links' => $departments->linkCollection()->toArray(),
        ]);
    }

    public function create()
    {
        $campuses = Campus::where('is_active', true)->get();
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Add Department',
            'subtitle' => 'Create an academic department inside a campus.',
            'submitUrl' => '/admin/departments',
            'method' => 'post',
            'cancelUrl' => '/admin/departments',
            'fields' => [
                ['name' => 'campus_id', 'label' => 'Campus', 'type' => 'select', 'required' => true,
                 'options' => $campuses->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])->values()->all()],
                ['name' => 'name', 'label' => 'Department Name', 'type' => 'text', 'required' => true],
                ['name' => 'code', 'label' => 'Code', 'type' => 'text'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Department Active', 'type' => 'checkbox'],
            ],
            'initialValues' => ['campus_id' => '', 'name' => '', 'code' => '', 'description' => '', 'is_active' => true],
        ]);
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
        $validated['is_active'] = $request->boolean('is_active');

        Department::create($validated);
        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $campuses = Campus::where('is_active', true)->get();
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Edit Department',
            'subtitle' => 'Update department settings and campus link.',
            'submitUrl' => "/admin/departments/{$department->id}",
            'method' => 'put',
            'cancelUrl' => '/admin/departments',
            'fields' => [
                ['name' => 'campus_id', 'label' => 'Campus', 'type' => 'select', 'required' => true,
                 'options' => $campuses->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])->values()->all()],
                ['name' => 'name', 'label' => 'Department Name', 'type' => 'text', 'required' => true],
                ['name' => 'code', 'label' => 'Code', 'type' => 'text'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ['name' => 'is_active', 'label' => 'Department Active', 'type' => 'checkbox'],
            ],
            'initialValues' => [
                'campus_id' => $department->campus_id, 'name' => $department->name,
                'code' => $department->code ?? '', 'description' => $department->description ?? '',
                'is_active' => (bool) $department->is_active,
            ],
        ]);
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
        $validated['is_active'] = $request->boolean('is_active');

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
