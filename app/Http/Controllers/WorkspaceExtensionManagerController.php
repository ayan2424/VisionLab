<?php

namespace App\Http\Controllers;

use App\Models\Extension;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceExtensionManagerController extends Controller
{
    private CodeServerManager $manager;

    public function __construct(CodeServerManager $manager)
    {
        $this->manager = $manager;
    }

    public function install(Request $request, Workspace $workspace, Extension $extension)
    {
        $user = Auth::user();
        
        // Ensure user is the instructor of the course this workspace belongs to
        if (!$user->isAdmin() && !($user->isInstructor() && $workspace->course?->instructor_id === $user->id)) {
            abort(403, 'Unauthorized to manage extensions for this workspace.');
        }

        // Add to workspace extensions policy
        $workspace->extensions()->firstOrCreate(['extension_id' => $extension->id]);

        // If workspace is running, try to install it natively
        if ($workspace->isRunning()) {
            // Note: In a real implementation, this would trigger `docker exec` via CodeServerManager.
            // We simulate it here as requested.
            // $this->manager->installExtension($workspace, $extension->package_identifier);
        }

        return back()->with('success', "Extension {$extension->name} queued for installation.");
    }

    public function uninstall(Request $request, Workspace $workspace, Extension $extension)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && !($user->isInstructor() && $workspace->course?->instructor_id === $user->id)) {
            abort(403, 'Unauthorized to manage extensions for this workspace.');
        }

        $workspace->extensions()->where('extension_id', $extension->id)->delete();

        // If workspace is running, try to uninstall it natively
        if ($workspace->isRunning()) {
            // $this->manager->uninstallExtension($workspace, $extension->package_identifier);
        }

        return back()->with('success', "Extension {$extension->name} queued for removal.");
    }
}
