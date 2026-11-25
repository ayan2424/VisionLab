<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkspaceTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminTemplateController extends Controller
{
    public function index()
    {
        $templates = WorkspaceTemplate::latest()->get();
        return Inertia::render('Admin/Templates/Index', compact('templates'));
    }

    public function create()
    {
        return Inertia::render('Admin/Templates/Form');
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
            'bootstrap_script' => 'nullable|string',
            'ui_parameters' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        if (!empty($validated['ui_parameters'])) {
            $decoded = json_decode($validated['ui_parameters'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['ui_parameters'] = $decoded;
            } else {
                return back()->withInput()->withErrors(['ui_parameters' => 'Invalid JSON format for UI Parameters.']);
            }
        }

        WorkspaceTemplate::create($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Workspace template created successfully.');
    }

    public function edit(WorkspaceTemplate $template)
    {
        return Inertia::render('Admin/Templates/Form', compact('template'));
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
            'bootstrap_script' => 'nullable|string',
            'ui_parameters' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        if (!empty($validated['ui_parameters'])) {
            $decoded = json_decode($validated['ui_parameters'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['ui_parameters'] = $decoded;
            } else {
                return back()->withInput()->withErrors(['ui_parameters' => 'Invalid JSON format for UI Parameters.']);
            }
        } else {
            $validated['ui_parameters'] = null;
        }

        $template->update($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Workspace template updated successfully.');
    }

    public function destroy(WorkspaceTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', 'Workspace template deleted successfully.');
    }
}
