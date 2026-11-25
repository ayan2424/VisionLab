<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\AuditLog;
use App\Services\CodeServerManager;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminWorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Workspace::with(['owner', 'course']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $workspaces = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Workspaces/Index', [
            'workspaces'   => $workspaces,
            'statusFilter' => (string) $request->get('status', ''),
        ]);
    }

    public function show(Workspace $workspace)
    {
        $workspace->load(['owner', 'course', 'extensions.extension']);
        $auditLogs = AuditLog::forResource('Workspace', $workspace->id)
            ->with('actor')
            ->latest()
            ->take(20)
            ->get();

        // All available extensions for the toggle UI
        $allExtensions = \App\Models\Extension::where('is_active', true)->orderBy('name')->get();

        // Map of extension_id => WorkspaceExtension pivot for this workspace
        $workspaceExtMap = $workspace->extensions->keyBy('extension_id');

        return Inertia::render('Admin/Workspaces/Show', compact('workspace', 'auditLogs', 'allExtensions', 'workspaceExtMap'));
    }

    public function stop(Workspace $workspace)
    {
        if ($workspace->status !== 'running') {
            return back()->with('error', 'Workspace is not running.');
        }

        $manager = new CodeServerManager();
        $manager->stopWorkspace($workspace);

        AuditLog::record(
            action: 'admin_force_stopped_workspace',
            resourceType: 'Workspace',
            resourceId: $workspace->id,
            oldState: ['status' => 'running'],
            newState: ['status' => 'stopped', 'reason' => 'Admin manual force stop'],
        );

        return back()->with('success', 'Workspace forcefully stopped.');
    }

    public function archive(Workspace $workspace)
    {
        $previousStatus = $workspace->status;

        if ($workspace->status === 'running') {
            $manager = new CodeServerManager();
            $manager->stopWorkspace($workspace);
        }

        $workspace->update(['status' => 'archived']);

        AuditLog::record(
            action: 'admin_archived_workspace',
            resourceType: 'Workspace',
            resourceId: $workspace->id,
            oldState: ['status' => $previousStatus],
            newState: ['status' => 'archived', 'reason' => 'Admin archive'],
        );

        return back()->with('success', 'Workspace archived successfully.');
    }

    public function destroy(Workspace $workspace)
    {
        $manager = new CodeServerManager();
        $manager->deleteWorkspace($workspace);

        $workspaceId = $workspace->id;
        $previousStatus = $workspace->status;
        $workspace->delete();

        AuditLog::record(
            action: 'admin_deleted_workspace',
            resourceType: 'Workspace',
            resourceId: $workspaceId,
            oldState: ['status' => $previousStatus],
            newState: ['deleted' => true, 'reason' => 'Admin permanent deletion (files wiped)'],
        );

        return back()->with('success', 'Workspace and its physical files permanently deleted.');
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
