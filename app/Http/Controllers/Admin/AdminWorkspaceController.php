<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\CodeServerManager;

class AdminWorkspaceController extends Controller
{
    private CodeServerManager $codeServerManager;

    public function __construct(CodeServerManager $codeServerManager)
    {
        $this->codeServerManager = $codeServerManager;
    }

    public function index()
    {
        $workspaces = Room::with('owner')
            ->latest()
            ->paginate(20);

        // Gather container status for each workspace
        $statuses = [];
        foreach ($workspaces as $workspace) {
            try {
                $statuses[$workspace->id] = $this->codeServerManager->getStatus($workspace);
            } catch (\Throwable $e) {
                $statuses[$workspace->id] = ['running' => false, 'url' => null, 'port' => null, 'container_id' => null];
            }
        }

        return view('admin.workspaces.index', compact('workspaces', 'statuses'));
    }

    public function show(Room $workspace)
    {
        $workspace->load('owner', 'members.user');
        $status = $this->codeServerManager->getStatus($workspace);

        // List workspace files
        $files = [];
        try {
            $files = $this->codeServerManager->listFiles($workspace);
        } catch (\Throwable $e) {
            // Workspace dir may not exist yet
        }

        return view('admin.workspaces.show', compact('workspace', 'status', 'files'));
    }

    public function stop(Room $workspace)
    {
        $this->codeServerManager->stopWorkspace($workspace);

        return back()->with('success', "Workspace \"{$workspace->name}\" stopped.");
    }

    public function destroy(Room $workspace)
    {
        $this->codeServerManager->stopWorkspace($workspace);
        $workspace->delete();

        return redirect()->route('admin.workspaces.index')
            ->with('success', "Workspace \"{$workspace->name}\" deleted permanently.");
    }
}
