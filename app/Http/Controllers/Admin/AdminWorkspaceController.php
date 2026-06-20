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
        $workspace->load(['owner', 'course', 'collaborators']);
        $auditLogs = AuditLog::where('workspace_id', $workspace->id)
            ->with('actor')
            ->latest()
            ->take(20)
            ->get();
            
        return view('admin.workspaces.show', compact('workspace', 'auditLogs'));
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
}
