<?php

namespace App\Http\Controllers;

use App\Services\CodeServerManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * WorkspaceFileController — File I/O API for workspace file explorer.
 *
 * All paths are sandboxed to the workspace directory via CodeServerManager.
 * Logs all write operations for audit trail.
 */
class WorkspaceFileController extends Controller
{
    private CodeServerManager $codeServer;

    public function __construct(CodeServerManager $codeServer)
    {
        $this->codeServer = $codeServer;
    }

    /**
     * GET /api/workspace/{slug}/files
     * Returns the recursive file tree for the workspace.
     */
    public function list(string $slug): JsonResponse
    {
        $workspace = $this->resolveWorkspace($slug);
        if (!$workspace) return response()->json(['error' => 'Workspace not found'], 404);

        $tree = $this->codeServer->listFiles($workspace);

        return response()->json(['files' => $tree]);
    }

    /**
     * GET /api/workspace/{slug}/read-file?path=relative/path
     * Returns the content of a specific file.
     */
    public function read(Request $request, string $slug): JsonResponse
    {
        $workspace = $this->resolveWorkspace($slug);
        if (!$workspace) return response()->json(['error' => 'Workspace not found'], 404);

        $path = $request->query('path');
        if (!$path) return response()->json(['error' => 'Path required'], 400);

        $content = $this->codeServer->readFile($workspace, $path);
        if ($content === null) {
            return response()->json(['error' => 'File not found or access denied'], 404);
        }

        return response()->json([
            'path'    => $path,
            'content' => $content,
            'size'    => strlen($content),
        ]);
    }

    /**
     * POST /api/workspace/{slug}/write-file
     * Write content to a file (create or overwrite).
     */
    public function write(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'path'    => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $workspace = $this->resolveWorkspace($slug);
        if (!$workspace) return response()->json(['error' => 'Workspace not found'], 404);

        // Sandbox check: block dangerous paths
        $path = $request->input('path');
        if ($this->isDangerousPath($workspace, $path)) {
            return response()->json(['error' => 'Write access denied for this path'], 403);
        }

        $success = $this->codeServer->writeFile($workspace, $path, $request->input('content'));

        if ($success) {
            // Log the write action
            if (Auth::check()) {
                \App\Models\AiActionsLog::create([
                    'user_id'       => Auth::id(),
                    'workspace_ref' => $slug,
                    'action_type'   => 'file_write',
                    'file_path'     => $path,
                    'diff_summary'  => 'Manual write via file explorer',
                ]);
            }
        }

        return response()->json(['success' => $success]);
    }

    /**
     * POST /api/workspace/{slug}/create-file
     * Create a new empty file or directory.
     */
    public function create(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'path'         => 'required|string|max:500',
            'is_directory' => 'sometimes|boolean',
        ]);

        $workspace = $this->resolveWorkspace($slug);
        if (!$workspace) return response()->json(['error' => 'Workspace not found'], 404);

        $success = $this->codeServer->createFile(
            $workspace,
            $request->input('path'),
            $request->boolean('is_directory')
        );

        return response()->json(['success' => $success]);
    }

    /**
     * POST /api/workspace/{slug}/delete-file
     * Delete a file or directory.
     */
    public function delete(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'path' => 'required|string|max:500',
        ]);

        $workspace = $this->resolveWorkspace($slug);
        if (!$workspace) return response()->json(['error' => 'Workspace not found'], 404);

        $path = $request->input('path');
        if ($this->isDangerousPath($workspace, $path)) {
            return response()->json(['error' => 'Delete access denied for this path'], 403);
        }

        $success = $this->codeServer->deleteFile($workspace, $path);

        if ($success && Auth::check()) {
            \App\Models\AiActionsLog::create([
                'user_id'       => Auth::id(),
                'workspace_ref' => $slug,
                'action_type'   => 'file_delete',
                'file_path'     => $path,
                'diff_summary'  => 'File deleted via explorer',
            ]);
        }

        return response()->json(['success' => $success]);
    }

    /**
     * POST /api/workspace/{slug}/rename-file
     * Rename a file or directory.
     */
    public function rename(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'old_path' => 'required|string|max:500',
            'new_path' => 'required|string|max:500',
        ]);

        $workspace = $this->resolveWorkspace($slug);
        if (!$workspace) return response()->json(['error' => 'Workspace not found'], 404);

        $success = $this->codeServer->renameFile(
            $workspace,
            $request->input('old_path'),
            $request->input('new_path')
        );

        return response()->json(['success' => $success]);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function resolveWorkspace(string $slug): ?\App\Models\Workspace
    {
        $id = str_replace('ws-', '', $slug);
        return \App\Models\Workspace::find($id);
    }

    /**
     * Block writes to dangerous paths (sandbox enforcement).
     */
    private function isDangerousPath(\App\Models\Workspace $workspace, string $path): bool
    {
        $normalized = strtolower(str_replace('\\', '/', $path));

        // Block path traversal attempts in the relative string (early exit)
        if (str_contains($normalized, '..')) {
            Log::warning('Path traversal attempt blocked via relative path inspection', ['path' => $path]);
            return true;
        }

        // Validate via realpath in CodeServerManager
        $securePath = $this->codeServer->resolveSecurePath($workspace, $path);
        if ($securePath === null) {
            // resolveSecurePath already logs path traversal
            return true;
        }

        // Check if the secure absolute path touches protected directories
        $blocked = [
            DIRECTORY_SEPARATOR . '.env',
            DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR,
        ];

        foreach ($blocked as $pattern) {
            if (str_contains($securePath, $pattern)) {
                Log::warning('Write attempt to protected directory blocked', ['path' => $securePath]);
                return true;
            }
        }

        return false;
    }
}
