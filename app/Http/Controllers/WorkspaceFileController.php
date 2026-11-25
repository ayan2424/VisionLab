<?php

namespace App\Http\Controllers;

use App\Models\AiActionsLog;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * WorkspaceFileController — File I/O API for workspace file explorer.
 *
 * All paths are validated and sandboxed through CodeServerManager.
 * Logs all write and delete operations for audit trail.
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
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('readFiles', $workspace);

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
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('readFiles', $workspace);

        $path = $request->query('path');
        if (!$path) {
            return response()->json(['error' => 'Path required'], 400);
        }

        if ($this->isDangerousPath((string) $path)) {
            return response()->json(['error' => 'File not found or access denied'], 404);
        }

        $content = $this->codeServer->readFile($workspace, (string) $path);
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
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        $path = (string) $request->input('path');
        if ($this->isDangerousPath($path)) {
            return response()->json(['error' => 'Write access denied for this path'], 403);
        }

        $success = $this->codeServer->writeFile($workspace, $path, (string) $request->input('content'));

        if ($success && Auth::check()) {
            AiActionsLog::create([
                'user_id'       => Auth::id(),
                'workspace_ref' => $slug,
                'action_type'   => 'file_write',
                'file_path'     => $path,
                'diff_summary'  => 'Manual write via file explorer',
            ]);
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
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        $path = (string) $request->input('path');
        if ($this->isDangerousPath($path)) {
            return response()->json(['error' => 'Create access denied for this path'], 403);
        }

        $success = $this->codeServer->createFile(
            $workspace,
            $path,
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
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        $path = (string) $request->input('path');
        if ($this->isDangerousPath($path)) {
            return response()->json(['error' => 'Delete access denied for this path'], 403);
        }

        $success = $this->codeServer->deleteFile($workspace, $path);

        if ($success && Auth::check()) {
            AiActionsLog::create([
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
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        $oldPath = (string) $request->input('old_path');
        $newPath = (string) $request->input('new_path');
        if ($this->isDangerousPath($oldPath) || $this->isDangerousPath($newPath)) {
            return response()->json(['error' => 'Rename access denied for this path'], 403);
        }

        $success = $this->codeServer->renameFile($workspace, $oldPath, $newPath);

        return response()->json(['success' => $success]);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function resolveWorkspace(string $slug): ?Workspace
    {
        $stripped = str_starts_with($slug, 'ws-') ? substr($slug, 3) : $slug;

        if (ctype_digit($stripped)) {
            $workspace = Workspace::find((int) $stripped);
            if ($workspace !== null) {
                return $workspace;
            }
        }

        return Workspace::where('slug', $slug)->first();
    }

    /**
     * Enforce unified workspace sandbox validation for traversal, sensitive files, and shell payloads.
     */
    private function isDangerousPath(string $path): bool
    {
        return $this->codeServer->sanitizeWorkspaceRelativePath($path) === null;
    }
}
