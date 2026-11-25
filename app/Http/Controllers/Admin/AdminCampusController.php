<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCampusController extends Controller
{
    public function index()
    {
        $campuses = Campus::latest()->paginate(20);
        return Inertia::render('Admin/Erp/ResourceIndex', [
            'title' => 'Campuses',
            'subtitle' => 'Manage physical university campuses and contact endpoints.',
            'basePath' => '/admin/campuses',
            'createLabel' => 'Add Campus',
            'columns' => [
                ['key' => 'name', 'label' => 'Campus Name'],
                ['key' => 'code', 'label' => 'Code', 'mono' => true],
                ['key' => 'city', 'label' => 'City'],
                ['key' => 'email', 'label' => 'Contact Email'],
                ['key' => 'is_active', 'label' => 'Status', 'badge' => true],
            ],
            'rows' => $campuses->items(),
            'links' => $campuses->linkCollection()->toArray(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Add Campus',
            'subtitle' => 'Register a new university campus location.',
            'submitUrl' => '/admin/campuses',
            'method' => 'post',
            'cancelUrl' => '/admin/campuses',
            'fields' => [
                ['name' => 'name', 'label' => 'Campus Name', 'type' => 'text', 'required' => true],
                ['name' => 'code', 'label' => 'Campus Code', 'type' => 'text'],
                ['name' => 'city', 'label' => 'City', 'type' => 'text'],
                ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Contact Email', 'type' => 'email'],
                ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
                ['name' => 'is_active', 'label' => 'Campus Active', 'type' => 'checkbox'],
            ],
            'initialValues' => ['name' => '', 'code' => '', 'city' => '', 'address' => '', 'email' => '', 'phone' => '', 'is_active' => true],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:campuses',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        Campus::create($validated);
        return redirect()->route('admin.campuses.index')->with('success', 'Campus created successfully.');
    }

    public function edit(Campus $campus)
    {
        return Inertia::render('Admin/Erp/ResourceForm', [
            'title' => 'Edit Campus',
            'subtitle' => 'Update institutional campus metadata.',
            'submitUrl' => "/admin/campuses/{$campus->id}",
            'method' => 'put',
            'cancelUrl' => '/admin/campuses',
            'fields' => [
                ['name' => 'name', 'label' => 'Campus Name', 'type' => 'text', 'required' => true],
                ['name' => 'code', 'label' => 'Campus Code', 'type' => 'text'],
                ['name' => 'city', 'label' => 'City', 'type' => 'text'],
                ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Contact Email', 'type' => 'email'],
                ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
                ['name' => 'is_active', 'label' => 'Campus Active', 'type' => 'checkbox'],
            ],
            'initialValues' => [
                'name' => $campus->name, 'code' => $campus->code ?? '', 'city' => $campus->city ?? '',
                'address' => $campus->address ?? '', 'email' => $campus->email ?? '', 'phone' => $campus->phone ?? '',
                'is_active' => (bool) $campus->is_active,
            ],
        ]);
    }

    public function update(Request $request, Campus $campus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:campuses,name,' . $campus->id,
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $campus->update($validated);
        return redirect()->route('admin.campuses.index')->with('success', 'Campus updated successfully.');
    }

    public function destroy(Campus $campus)
    {
        // Prevent deletion if departments are linked
        if ($campus->departments()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete campus because it has active departments.']);
        }
        
        $campus->delete();
        return redirect()->route('admin.campuses.index')->with('success', 'Campus deleted successfully.');
    }
}
