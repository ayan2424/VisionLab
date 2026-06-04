<?php

namespace App\Http\Controllers;

use App\Models\AiActionsLog;
use App\Models\Room;
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
        $room = $this->resolveRoom($slug);
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $tree = $this->codeServer->listFiles($room);

        return response()->json(['files' => $tree]);
    }

    /**
     * GET /api/workspace/{slug}/read-file?path=relative/path
     * Returns the content of a specific file.
     */
    public function read(Request $request, string $slug): JsonResponse
    {
        $room = $this->resolveRoom($slug);
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $path = $request->query('path');
        if (! $path) {
            return response()->json(['error' => 'Path required'], 400);
        }

        $content = $this->codeServer->readFile($room, $path);
        if ($content === null) {
            return response()->json(['error' => 'File not found or access denied'], 404);
        }

        return response()->json([
            'path' => $path,
            'content' => $content,
            'size' => strlen($content),
        ]);
    }

    /**
     * POST /api/workspace/{slug}/write-file
     * Write content to a file (create or overwrite).
     */
    public function write(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'path' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $room = $this->resolveRoom($slug);
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        // Sandbox check: block dangerous paths
        $path = $request->input('path');
        if ($this->isDangerousPath($path)) {
            return response()->json(['error' => 'Write access denied for this path'], 403);
        }

        $success = $this->codeServer->writeFile($room, $path, $request->input('content'));

        if ($success) {
            // Log the write action
            if (Auth::check()) {
                AiActionsLog::create([
                    'user_id' => Auth::id(),
                    'workspace_ref' => $room->slug,
                    'action_type' => 'file_write',
                    'file_path' => $path,
                    'diff_summary' => 'Manual write via file explorer',
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
            'path' => 'required|string|max:500',
            'is_directory' => 'sometimes|boolean',
        ]);

        $room = $this->resolveRoom($slug);
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $success = $this->codeServer->createFile(
            $room,
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

        $room = $this->resolveRoom($slug);
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $path = $request->input('path');
        if ($this->isDangerousPath($path)) {
            return response()->json(['error' => 'Delete access denied for this path'], 403);
        }

        $success = $this->codeServer->deleteFile($room, $path);

        if ($success && Auth::check()) {
            AiActionsLog::create([
                'user_id' => Auth::id(),
                'workspace_ref' => $room->slug,
                'action_type' => 'file_delete',
                'file_path' => $path,
                'diff_summary' => 'File deleted via explorer',
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

        $room = $this->resolveRoom($slug);
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $success = $this->codeServer->renameFile(
            $room,
            $request->input('old_path'),
            $request->input('new_path')
        );

        return response()->json(['success' => $success]);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function resolveRoom(string $slug): ?Room
    {
        return Room::where('slug', $slug)->first();
    }

    /**
     * Block writes to dangerous paths (sandbox enforcement).
     */
    private function isDangerousPath(string $path): bool
    {
        $normalized = strtolower(str_replace('\\', '/', $path));

        $blocked = ['.env', 'vendor/', 'storage/', 'bootstrap/', 'node_modules/'];

        foreach ($blocked as $pattern) {
            if (str_contains($normalized, $pattern)) {
                return true;
            }
        }

        // Block path traversal attempts
        if (str_contains($normalized, '..')) {
            return true;
        }

        return false;
    }
}
