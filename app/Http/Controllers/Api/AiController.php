<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiPendingPatch;
use App\Models\Room;
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
     * OpenAI-compatible completions endpoint for Continue.
     */
    public function chatCompletions(Request $request)
    {
        // Must have X-Workspace-Id or fallback to find any workspace owned by user for MVP
        $workspaceId = $request->header('X-Workspace-Id');

        if ($workspaceId) {
            $room = Room::where('slug', $workspaceId)->orWhere('id', $workspaceId)->first();
        } else {
            // Fallback for MVP if header isn't passed correctly by Continue
            $room = Room::where('owner_id', Auth::id())->first();
        }

        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $messages = $request->input('messages', []);
        $stream = $request->input('stream', true);
        $mode = $request->header('X-Ai-Mode', 'AGENT'); // CHAT, PLAN, AGENT
        $model = $request->input('model', 'claude-3-5-sonnet-20241022');

        if ($stream) {
            $response = new StreamedResponse(function () use ($room, $messages, $mode, $model) {
                // Keep output buffer clear
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }

                foreach ($this->aiService->handleChatCompletion($room, $messages, $mode, $model) as $chunk) {
                    echo $chunk;
                    flush();
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
     * Approve a proposed patch.
     */
    public function approvePatch(Request $request, AiPendingPatch $patch)
    {
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
        if ($patch->status !== 'pending') {
            return response()->json(['message' => 'Patch is no longer pending.'], 400);
        }

        $patch->update(['status' => 'rejected']);

        return response()->json(['message' => 'Patch rejected.']);
    }

    /**
     * Background execution of an AI Plan.
     */
    public function executePlan(Request $request)
    {
        $workspaceId = $request->input('workspaceId') ?? $request->header('X-Workspace-Id');

        if ($workspaceId) {
            $room = Room::where('slug', $workspaceId)->orWhere('id', $workspaceId)->first();
        } else {
            $room = Room::where('owner_id', Auth::id())->first();
        }

        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        // Trigger the background execution
        // We will run this synchronously for now to ensure patches are proposed
        $this->aiService->executePlan($room, Auth::user());

        return response()->json(['message' => 'Plan execution started']);
    }
}
