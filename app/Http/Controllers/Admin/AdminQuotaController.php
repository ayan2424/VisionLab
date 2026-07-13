<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkspaceQuota;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminQuotaController extends Controller
{
    public function index()
    {
        // For simplicity, we just list the global/role-based quotas.
        // A complete implementation would also list course or user-specific overrides.
        $quotas = WorkspaceQuota::orderBy('scope')->get();
        return view('admin.quotas.index', compact('quotas'));
    }

    public function updateGlobal(Request $request)
    {
        $request->validate([
            'quotas' => 'required|array',
            'quotas.*.id' => 'required|exists:workspace_quotas,id',
            'quotas.*.max_workspaces' => 'required|integer|min:1',
            'quotas.*.memory_mb' => 'required|integer',
            'quotas.*.cpu_shares' => 'required|integer',
            'quotas.*.disk_mb' => 'required|integer',
            'quotas.*.timeout_minutes' => 'required|integer|min:5',
        ]);

        foreach ($request->quotas as $qData) {
            $quota = WorkspaceQuota::find($qData['id']);
            $oldData = $quota->toArray();
            
            $quota->update([
                'max_workspaces' => $qData['max_workspaces'],
                'memory_mb' => $qData['memory_mb'],
                'cpu_shares' => $qData['cpu_shares'],
                'disk_mb' => $qData['disk_mb'],
                'timeout_minutes' => $qData['timeout_minutes'],
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'admin_updated_quota',
                'resource_type' => 'WorkspaceQuota',
                'resource_id' => $quota->id,
                'details' => [
                    'old' => $oldData,
                    'new' => $quota->toArray(),
                ],
                'ip_address' => request()->ip(),
            ]);
        }

        return back()->with('success', 'Quotas updated successfully.');
    }
}
