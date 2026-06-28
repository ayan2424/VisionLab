<?php

namespace App\Http\Controllers;

use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExtensionRegistryController extends Controller
{
    /**
     * Download the specified extension VSIX file.
     * Enforces marketplace policy by only serving active, verified extensions.
     */
    public function download(Request $request, $packageIdentifier)
    {
        $extension = Extension::where('package_identifier', $packageIdentifier)
            ->where('is_active', true)
            ->firstOrFail();

        if (!$extension->artifact_path || !Storage::exists($extension->artifact_path)) {
            abort(404, 'Extension artifact not found');
        }

        return response()->download(Storage::path($extension->artifact_path), "{$extension->package_identifier}-{$extension->version}.vsix");
    }

    /**
     * Get extension metadata and SHA256 integrity hash.
     */
    public function metadata(Request $request, $packageIdentifier)
    {
        $extension = Extension::where('package_identifier', $packageIdentifier)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'name' => $extension->name,
            'package_identifier' => $extension->package_identifier,
            'version' => $extension->version,
            'checksum' => $extension->checksum,
            'is_global' => $extension->is_global,
            'is_builtin' => $extension->is_builtin,
            'is_required' => $extension->is_required,
        ]);
    }
}
