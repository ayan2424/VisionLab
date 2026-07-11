<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;

class AdminCampusController extends Controller
{
    public function index()
    {
        $campuses = Campus::latest()->paginate(20);
        return view('admin.erp.campuses.index', compact('campuses'));
    }

    public function create()
    {
        return view('admin.erp.campuses.create');
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
        $validated['is_active'] = $request->has('is_active');

        Campus::create($validated);
        return redirect()->route('admin.campuses.index')->with('success', 'Campus created successfully.');
    }

    public function edit(Campus $campus)
    {
        return view('admin.erp.campuses.edit', compact('campus'));
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
        $validated['is_active'] = $request->has('is_active');

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
