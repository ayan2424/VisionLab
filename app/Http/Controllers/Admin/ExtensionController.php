<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index(Request $request)
    {
        $query = Extension::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('package_identifier', 'like', "%{$search}%");
        }

        $extensions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.extensions.index', compact('extensions'));
    }

    public function create()
    {
        return view('admin.extensions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'package_identifier' => 'required|string|max:150|unique:extensions',
            'version'            => 'required|string|max:20',
            'description'        => 'nullable|string|max:500',
            'is_global'          => 'sometimes|boolean',
        ]);

        Extension::create(array_merge($validated, ['is_builtin' => false, 'is_active' => true]));

        return redirect()->route('admin.extensions.index')->with('success', 'Extension added.');
    }

    public function edit(Extension $extension)
    {
        return view('admin.extensions.edit', compact('extension'));
    }

    public function update(Request $request, Extension $extension)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'version'     => 'required|string|max:20',
            'description' => 'nullable|string|max:500',
            'is_global'   => 'sometimes|boolean',
        ]);

        $validated['is_global'] = $request->boolean('is_global');
        $extension->update($validated);

        return redirect()->route('admin.extensions.index')->with('success', 'Extension updated.');
    }

    public function toggleGlobal(Extension $extension)
    {
        $extension->update(['is_active' => !$extension->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $extension->is_active,
            'message'   => $extension->is_active ? 'Extension enabled globally.' : 'Extension disabled.',
        ]);
    }

    public function destroy(Extension $extension)
    {
        if ($extension->is_builtin) {
            return back()->withErrors(['error' => 'Cannot delete built-in extensions.']);
        }
        $extension->delete();
        return redirect()->route('admin.extensions.index')->with('success', 'Extension deleted.');
    }
}
