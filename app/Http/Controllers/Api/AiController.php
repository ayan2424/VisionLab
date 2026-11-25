<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiPendingPatch;
use App\Models\Workspace;
use App\Services\AiSandbox;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{
    private AiService $aiService;
    private AiSandbox $aiSandbox;

    public function __construct(AiService $aiService, AiSandbox $aiSandbox)
    {
        $this->aiService = $aiService;
        $this->aiSandbox = $aiSandbox;
    }

    /**
     * Resolve a workspace by numeric ID (with optional ws- prefix) or slug.
     */
    private function resolveWorkspace(?string $identifier): ?Workspace
    {
        if ($identifier === null || trim($identifier) === '') {
            return null;
        }

        $trimmed = trim($identifier);
        $stripped = str_starts_with($trimmed, 'ws-') ? substr($trimmed, 3) : $trimmed;

        if (ctype_digit($stripped)) {
            $workspace = Workspace::find((int) $stripped);
            if ($workspace !== null) {
                return $workspace;
            }
        }

        return Workspace::where('slug', $trimmed)->first();
    }

    /**
     * OpenAI-compatible completions endpoint for Continue.
     */
    public function chatCompletions(Request $request)
    {
        $workspaceHeader = $request->header('X-Workspace-Id');

        if ($workspaceHeader !== null && trim($workspaceHeader) !== '') {
            $workspace = $this->resolveWorkspace($workspaceHeader);
        } else {
            $workspace = Workspace::where('student_id', Auth::id())->first();
        }

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('access', $workspace);

        $messages = $request->input('messages', []);
        $stream = $request->input('stream', true);
        $mode = $request->header('X-Ai-Mode', 'AGENT'); // CHAT, PLAN, AGENT
        $requestedModel = $request->input('model', 'claude-3-5-sonnet-20241022');

        if ($stream) {
            $response = new StreamedResponse(function () use ($workspace, $messages, $mode, $requestedModel) {
                if (!app()->runningUnitTests() && ob_get_level() > 0) {
                    ob_end_clean();
                }

                foreach ($this->aiService->handleChatCompletion($workspace, $messages, $mode, true, Auth::id(), $requestedModel) as $chunk) {
                    echo $chunk;
                    if (!app()->runningUnitTests()) {
                        flush();
                    }
                }
            });

            $response->headers->set('Content-Type', 'text/event-stream');
            $response->headers->set('Cache-Control', 'no-cache');
            $response->headers->set('Connection', 'keep-alive');
            $response->headers->set('X-Accel-Buffering', 'no');

            return $response;
        }

        return response()->json(['error' => 'Non-streamed requests not fully implemented'], 501);
    }

    /**
     * Get pending patches for an authorized workspace.
     */
    public function pendingPatches(Request $request)
    {
        $workspaceId = $request->query('workspace_id');
        if (!$workspaceId) {
            return response()->json(['error' => 'workspace_id is required'], 400);
        }

        $workspace = $this->resolveWorkspace((string) $workspaceId);
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('view', $workspace);

        $patches = AiPendingPatch::where('workspace_id', $workspace->id)
            ->where('status', 'pending')
            ->get();

        return response()->json($patches);
    }

    /**
     * Approve a proposed patch.
     */
    public function approvePatch(Request $request, AiPendingPatch $patch)
    {
        $workspace = $patch->workspace;
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        if ($patch->status !== 'pending') {
            return response()->json(['message' => 'Patch is no longer pending.'], 400);
        }

        $success = $this->aiSandbox->applyPatch($patch);

        if ($success) {
            return response()->json(['message' => 'Patch applied successfully.']);
        }

        return response()->json(['message' => 'Failed to apply patch.'], 500);
    }

    /**
     * Reject a proposed patch.
     */
    public function rejectPatch(Request $request, AiPendingPatch $patch)
    {
        $workspace = $patch->workspace;
        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        if ($patch->status !== 'pending') {
            return response()->json(['message' => 'Patch is no longer pending.'], 400);
        }

        $patch->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Patch rejected.']);
    }

    /**
     * Background execution of an AI Plan.
     */
    public function executePlan(Request $request)
    {
        $workspaceId = $request->input('workspaceId') ?? $request->header('X-Workspace-Id');

        if ($workspaceId !== null && trim((string) $workspaceId) !== '') {
            $workspace = $this->resolveWorkspace((string) $workspaceId);
        } else {
            $workspace = Workspace::where('student_id', Auth::id())->first();
        }

        if (!$workspace) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $this->authorize('writeFiles', $workspace);

        $this->aiService->executePlan($workspace, Auth::user());

        return response()->json(['message' => 'Plan execution started']);
    }
}
