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

    public static function pistonLanguage(string $lang): array
    {
        return match ($lang) {
            'python'     => ['language' => 'python',     'version' => '3.10.0'],
            'javascript' => ['language' => 'javascript', 'version' => '18.15.0'],
            'typescript' => ['language' => 'typescript', 'version' => '5.0.3'],
            'php'        => ['language' => 'php',        'version' => '8.2.3'],
            'java'       => ['language' => 'java',       'version' => '15.0.2'],
            'c'          => ['language' => 'c',          'version' => '10.2.0'],
            'cpp'        => ['language' => 'c++',        'version' => '10.2.0'],
            'rust'       => ['language' => 'rust',       'version' => '1.50.0'],
            'go'         => ['language' => 'go',         'version' => '1.16.2'],
            'ruby'       => ['language' => 'ruby',       'version' => '3.0.1'],
            'bash'       => ['language' => 'bash',       'version' => '5.2.0'],
            default      => ['language' => 'python',     'version' => '3.10.0'],
        };
    }

    /** Personal workspace — auto-provisions */
    public function index(Request $request)
    {
        $user = Auth::user();

        $workspace = Workspace::firstOrCreate(
            ['student_id' => $user->id, 'name' => 'personal-' . $user->id],
            [
                'course_id' => null,
                'language'  => 'python',
                'port'      => null,
                'token'     => Str::random(64),
                'status'    => 'pending',
            ]
        );

        $serverInfo = $this->codeServerManager->startWorkspace($workspace);

        if (isset($serverInfo['port'])) {
            $cookiePath = str_starts_with($serverInfo['url'], '/codeserver/') 
                ? '/codeserver/' . $serverInfo['port'] 
                : '/';
            cookie()->queue(
                'key',
                $serverInfo['token'],
                525600, // 1 year in minutes
                $cookiePath
            );
        }

        return view('workspace', [
            'user'            => $user,
            'workspace'       => $workspace->fresh(),
            'workspaceName'   => 'My Workspace',
            'roomSlug'        => 'personal-' . $user->id,
            'isCollaborative' => false,
            'reverbConfig'    => $this->reverbConfig(),
            'vscodeUrl'       => $serverInfo['url'],
        ]);
    }

    /** Named workspace by ID */
    public function show(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $user = Auth::user();
        $serverInfo = $this->codeServerManager->startWorkspace($workspace);

        if (isset($serverInfo['port'])) {
            $cookiePath = str_starts_with($serverInfo['url'], '/codeserver/') 
                ? '/codeserver/' . $serverInfo['port'] 
                : '/';
            cookie()->queue(
                'key',
                $serverInfo['token'],
                525600, // 1 year in minutes
                $cookiePath
            );
        }

        return view('workspace', [
            'user'            => $user,
            'workspace'       => $workspace->fresh(),
            'workspaceName'   => $workspace->name,
            'roomSlug'        => 'ws-' . $workspace->id,
            'isCollaborative' => $workspace->collaborators()->count() > 1,
            'reverbConfig'    => $this->reverbConfig(),
            'vscodeUrl'       => $serverInfo['url'],
        ]);
    }

    /** Start a workspace container */
    public function start(Workspace $workspace): JsonResponse
    {
        $this->authorize('access', $workspace);

        $result = $this->codeServerManager->startWorkspace($workspace);

        return response()->json([
            'status' => 'running',
            'url'    => $result['url'],
            'port'   => $result['port'],
        ]);
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

    /** Restart a workspace container */
    public function restart(Workspace $workspace): JsonResponse
    {
        $this->authorize('manage', $workspace);

        $this->codeServerManager->restartWorkspace($workspace);

        return response()->json(['status' => 'restarted']);
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

    /** Ping workspace readiness */
    public function ping(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $isReady = $this->codeServerManager->isWorkspaceReady($workspace);
        
        \Illuminate\Support\Facades\Log::info('Ping called for workspace ' . $workspace->id, ['ready' => $isReady]);

        return response()->json(['ready' => $isReady]);
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

    private function reverbConfig(): array
    {
        $host   = env('REVERB_HOST', 'localhost');
        $port   = (int) env('REVERB_PORT', 8080);
        $scheme = env('REVERB_SCHEME', 'http');

        if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.replit.dev')) {
            $host   = $_SERVER['HTTP_HOST'];
            $port   = 443;
            $scheme = 'https';
        } elseif ($envDomain = env('REPLIT_DEV_DOMAIN')) {
            $host   = $envDomain;
            $port   = 443;
            $scheme = 'https';
        }

        $key = env('REVERB_APP_KEY', 'visioncode-key');

        return compact('key', 'host', 'port', 'scheme');
    }
}
