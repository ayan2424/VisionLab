<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkspaceTemplate;
use Illuminate\Http\Request;

class AdminTemplateController extends Controller
{
    public function index()
    {
        $templates = WorkspaceTemplate::latest()->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'git_url' => 'nullable|url|max:255',
            'language' => 'required|string|max:50',
            'start_command' => 'nullable|string|max:255',
            'nix_config' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        WorkspaceTemplate::create($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Workspace template created successfully.');
    }

    public function edit(WorkspaceTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, WorkspaceTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'git_url' => 'nullable|url|max:255',
            'language' => 'required|string|max:50',
            'start_command' => 'nullable|string|max:255',
            'nix_config' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Workspace template updated successfully.');
    }

    public function destroy(WorkspaceTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', 'Workspace template deleted successfully.');
    }
}
