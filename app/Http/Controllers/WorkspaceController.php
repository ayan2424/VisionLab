<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    private CodeServerManager $codeServerManager;

    public function __construct(CodeServerManager $codeServerManager)
    {
        $this->codeServerManager = $codeServerManager;
    }

    /** List all student workspaces */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $workspaces = Workspace::where('student_id', $user->id)
            ->where('status', '!=', 'archived')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $quota = \App\Models\WorkspaceQuota::resolveFor($user->id);

        return view('workspaces.index', compact('workspaces', 'quota', 'user'));
    }

    /** Show workspace creation form */
    public function create()
    {
        $user = Auth::user();
        $templates = \App\Models\WorkspaceTemplate::where('is_active', true)->get();
        return view('workspaces.create', compact('templates', 'user'));
    }

    /** Create personal workspace */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|regex:/^[a-zA-Z0-9\-_ ]+$/',
            'template_id' => 'required|exists:workspace_templates,id',
        ]);

        $user = Auth::user();
        
        // Enforce Workspace Limits
        $quota = \App\Models\WorkspaceQuota::resolveFor($user->id);
        $activeWorkspacesCount = Workspace::where('student_id', $user->id)
            ->where('status', '!=', 'archived')
            ->count();
            
        if ($activeWorkspacesCount >= $quota->max_workspaces) {
            return redirect()->back()->with('error', "You have reached your limit of {$quota->max_workspaces} active workspaces. Please delete an existing workspace to create a new one.");
        }

        $template = \App\Models\WorkspaceTemplate::find($request->template_id);

        $workspace = Workspace::create([
            'student_id'  => $user->id,
            'name'        => trim($request->name),
            'course_id'   => null,
            'language'    => $template->language,
            'template_id' => $template->id,
            'port'        => null,
            'token'       => Str::random(64),
            'status'      => 'pending',
        ]);

        return redirect()->route('workspace.show', $workspace->slug)->with('success', 'Workspace provisioning started! Connecting to IDE...');
    }

    /** Named workspace by ID */
    public function show(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        $user = $request->user();

        return view('workspace', [
            'user'            => $user,
            'workspace'       => $workspace->fresh(),
            'workspaceName'   => $workspace->name,
            'roomSlug'        => 'ws-' . $workspace->id,
            'isCollaborative' => $workspace->collaborators()->count() > 1 && (!$workspace->assignment || $workspace->assignment->mode !== 'exam'),
            'reverbConfig'    => self::reverbConfig(),
            'vscodeUrl'       => $workspace->status === 'running' ? $workspace->proxy_url : null,
            'needsBoot'       => $workspace->status !== 'running',
            'isAssignmentLocked' => $workspace->assignment ? $workspace->assignment->is_locked : false,
        ]);
    }

    /** Start a workspace container */
    public function start(Workspace $workspace): JsonResponse
    {
        $this->authorize('access', $workspace);

        // Extend execution time to 10 minutes to allow heavy Nix bootstrap scripts to complete
        set_time_limit(600);

        $result = $this->codeServerManager->startWorkspace($workspace);

        if (!$result) {
            return response()->json(['status' => 'error', 'message' => 'Failed to start workspace'], 500);
        }

        // Send authentication cookie along with the response headers
        $cookiePath = str_starts_with($result['url'], '/ide/') 
            ? '/ide/' . $result['port'] 
            : '/';
            
        $cookie = cookie('key', $result['token'], 525600, $cookiePath);

        return response()->json([
            'status' => 'running',
            'url'    => $result['url'],
            'port'   => $result['port'],
        ])->cookie($cookie);
    }

    /** Ping workspace status */
    public function ping(Workspace $workspace): JsonResponse
    {
        $this->authorize('access', $workspace);
        
        $isReady = $this->codeServerManager->isWorkspaceReady($workspace);
        
        \Log::info("Ping workspace {$workspace->id}: ready={$isReady}, port={$workspace->port}, docker=".$this->codeServerManager->isDockerAvailable());
        
        return response()->json(['ready' => $isReady]);
    }

    /** Stop a workspace container */
    public function stop(Workspace $workspace): JsonResponse
    {
        $this->authorize('manage', $workspace);

        $this->codeServerManager->stopWorkspace($workspace);

        return response()->json(['status' => 'stopped']);
    }

    /** Destroy a workspace container and its persistent storage */
    public function destroy(Workspace $workspace)
    {
        $this->authorize('manage', $workspace);

        $this->codeServerManager->deleteWorkspace($workspace);
        $workspace->delete();

        return redirect()->route('workspace.index')->with('success', 'Workspace and its files have been permanently deleted.');
    }

    /** Restart a workspace container */
    public function restart(Workspace $workspace): JsonResponse
    {
        $this->authorize('manage', $workspace);

        $this->codeServerManager->stopWorkspace($workspace);
        $this->codeServerManager->startWorkspace($workspace);

        return response()->json(['status' => 'restarted']);
    }

    /** Show the environment rebuild preloader page */
    public function rebuildView(Workspace $workspace)
    {
        $this->authorize('access', $workspace);
        return view('workspaces.rebuild', compact('workspace'));
    }

    /** Process the rebuild request safely */
    public function processRebuild(Workspace $workspace): JsonResponse
    {
        $this->authorize('manage', $workspace);

        // Safely stop the container. The Docker volume remains untouched.
        $this->codeServerManager->stopWorkspace($workspace);

        // Update status to indicate it is booting again
        $workspace->update(['status' => 'starting']);

        // Start the workspace synchronously
        $this->codeServerManager->startWorkspace($workspace);

        return response()->json(['status' => 'rebuilding']);
    }

    /** Get workspace status */
    public function status(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $status = $this->codeServerManager->getStatus($workspace);
        $stats  = $this->codeServerManager->getStats($workspace);

        return response()->json([
            'status'    => $workspace->fresh()->status,
            'running'   => $status['running'],
            'url'       => $status['url'],
            'stats'     => $stats,
            'heartbeat' => $workspace->heartbeat_at?->diffForHumans(),
        ]);
    }

    // ── File I/O API ────────────────────────────────────────────────────

    /** List files in workspace */
    public function files(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $path  = $request->query('path', '');
        $files = $this->codeServerManager->listFiles($workspace, $path);

        return response()->json($files);
    }

    /** Read a file */
    public function readFile(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $path    = $request->query('path', '');
        $content = $this->codeServerManager->readFile($workspace, $path);

        if ($content === null) {
            return response()->json(['error' => 'File not found or access denied'], 404);
        }

        return response()->json(['path' => $path, 'content' => $content]);
    }

    /** Download a file */
    public function downloadFile(Workspace $workspace, Request $request)
    {
        $this->authorize('access', $workspace);

        $path = $request->query('path', '');
        $securePath = $this->codeServerManager->getSecureFilePath($workspace, $path);

        if ($securePath === null) {
            abort(404, 'File not found or access denied');
        }

        return response()->download($securePath);
    }

    /** Write a file */
    public function writeFile(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $request->validate([
            'path'    => 'required|string|max:500',
            'content' => 'required|string|max:1048576', // 1MB max
        ]);

        $success = $this->codeServerManager->writeFile(
            $workspace,
            $request->input('path'),
            $request->input('content')
        );

        if (!$success) {
            return response()->json(['error' => 'Write failed — path may be invalid'], 403);
        }

        return response()->json(['status' => 'written']);
    }

    /** Create a file or directory */
    public function createFile(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $request->validate([
            'path'         => 'required|string|max:500',
            'is_directory' => 'sometimes|boolean',
        ]);

        $success = $this->codeServerManager->createFile(
            $workspace,
            $request->input('path'),
            $request->boolean('is_directory')
        );

        if (!$success) {
            return response()->json(['error' => 'Creation failed'], 400);
        }

        return response()->json(['status' => 'created']);
    }

    /** Delete a file or directory */
    public function deleteFile(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $request->validate(['path' => 'required|string|max:500']);

        $success = $this->codeServerManager->deleteFile($workspace, $request->input('path'));

        return response()->json(['status' => $success ? 'deleted' : 'failed']);
    }

    /** Rename a file or directory */
    public function renameFile(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $request->validate([
            'old_path' => 'required|string|max:500',
            'new_path' => 'required|string|max:500',
        ]);

        $success = $this->codeServerManager->renameFile(
            $workspace,
            $request->input('old_path'),
            $request->input('new_path')
        );

        return response()->json(['status' => $success ? 'renamed' : 'failed']);
    }

    // ══════════════════════════════════════════════════════════════════════

    public static function reverbConfig(): array
    {
        $host   = request()->getHost() ?: env('REVERB_HOST', 'localhost');
        $scheme = request()->isSecure() ? 'https' : env('REVERB_SCHEME', 'http');
        $port   = request()->isSecure() ? 443 : (int) env('REVERB_PORT', 8080);

        $key = env('REVERB_APP_KEY', 'visioncode-key');

        return compact('key', 'host', 'port', 'scheme');
    }
}
