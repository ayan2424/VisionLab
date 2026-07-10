<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\AuditLog;
use App\Services\CodeServerManager;
use Illuminate\Http\Request;

class AdminWorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Workspace::with(['owner', 'course']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $workspaces = $query->latest()->paginate(20);

        return view('admin.workspaces.index', compact('workspaces'));
    }

    public function show(Workspace $workspace)
    {
        $workspace->load(['owner', 'course', 'collaborators', 'extensions.extension']);
        $auditLogs = AuditLog::where('workspace_id', $workspace->id)
            ->with('actor')
            ->latest()
            ->take(20)
            ->get();

        // All available extensions for the toggle UI
        $allExtensions = \App\Models\Extension::where('is_active', true)->orderBy('name')->get();

        // Map of extension_id => WorkspaceExtension pivot for this workspace
        $workspaceExtMap = $workspace->extensions->keyBy('extension_id');

        return view('admin.workspaces.show', compact('workspace', 'auditLogs', 'allExtensions', 'workspaceExtMap'));
    }

    public function stop(Workspace $workspace)
    {
        if ($workspace->status !== 'running') {
            return back()->with('error', 'Workspace is not running.');
        }

        $manager = new CodeServerManager();
        $manager->stopWorkspace($workspace);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_force_stopped_workspace',
            'resource_type' => 'Workspace',
            'resource_id' => $workspace->id,
            'details' => ['reason' => 'Admin manual force stop'],
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Workspace forcefully stopped.');
    }

    public function archive(Workspace $workspace)
    {
        if ($workspace->status === 'running') {
            $manager = new CodeServerManager();
            $manager->stopWorkspace($workspace);
        }

        $workspace->update(['status' => 'archived']);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_archived_workspace',
            'resource_type' => 'Workspace',
            'resource_id' => $workspace->id,
            'details' => ['reason' => 'Admin archive'],
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Workspace archived successfully.');
    }

    /**
     * Toggle a specific extension on/off for a specific workspace.
     */
    public function toggleExtension(Workspace $workspace, \App\Models\Extension $extension)
    {
        $pivot = \App\Models\WorkspaceExtension::firstOrCreate(
            ['workspace_id' => $workspace->id, 'extension_id' => $extension->id],
            ['is_enabled' => false, 'policy_source' => 'admin_manual', 'sync_status' => 'pending']
        );

        // Toggle the enabled state
        $pivot->update([
            'is_enabled'    => !$pivot->is_enabled,
            'sync_status'   => 'pending',
            'policy_source' => 'admin_manual',
        ]);

        // If workspace is running, dispatch sync job
        if ($workspace->isRunning()) {
            \App\Jobs\SyncWorkspaceExtensions::dispatch($workspace->id);
        }

        $action = $pivot->is_enabled ? 'enabled' : 'disabled';

        return response()->json([
            'success'    => true,
            'is_enabled' => $pivot->is_enabled,
            'message'    => "Extension {$extension->name} {$action} for this workspace.",
        ]);
    }
}
