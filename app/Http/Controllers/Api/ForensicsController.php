<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubmissionForensic;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForensicsController extends Controller
{
    /**
     * POST /api/workspace/{slug}/forensics/sync
     * Receives telemetry from the VS Code extension about human vs AI contribution.
     */
    public function sync(string $slug, Request $request): JsonResponse
    {
        $workspace = Workspace::where('slug', $slug)->firstOrFail();
        
        $this->authorize('access', $workspace);

        $validated = $request->validate([
            'humanKeystrokeDelta' => 'required|integer|min:0',
            'aiInjectedCharDelta' => 'required|integer|min:0',
        ]);

        // In a real scenario, the workspace must be linked to a specific submission.
        // For VisionLab's current schema, we find the active or latest assignment submission for this user/course.
        // If there's no active submission, we might just log it globally or skip.
        // For simplicity, we'll find the pending submission for the workspace's course and student.
        
        if (!$workspace->course_id) {
            return response()->json(['status' => 'ignored', 'reason' => 'No course linked to workspace']);
        }

        $submission = \App\Models\Submission::where('student_id', $workspace->student_id)
            ->whereHas('assignment', function ($q) use ($workspace) {
                $q->where('course_id', $workspace->course_id);
            })
            ->whereIn('status', ['not_started', 'in_progress'])
            ->latest()
            ->first();

        if (!$submission) {
            return response()->json(['status' => 'ignored', 'reason' => 'No active submission found']);
        }

        // Find or create the forensic record
        $forensic = SubmissionForensic::firstOrCreate(
            ['submission_id' => $submission->id],
            [
                'human_keystroke_count' => 0,
                'ai_injected_char_count' => 0,
                'pasted_char_count' => 0,
                'human_percentage' => 100,
                'ai_percentage' => 0,
                'confidence_level' => 'high'
            ]
        );

        // Update counts
        $forensic->human_keystroke_count += $validated['humanKeystrokeDelta'];
        $forensic->ai_injected_char_count += $validated['aiInjectedCharDelta'];

        // Recalculate percentages
        $total = $forensic->human_keystroke_count + $forensic->ai_injected_char_count + $forensic->pasted_char_count;
        
        if ($total > 0) {
            $forensic->human_percentage = round(($forensic->human_keystroke_count / $total) * 100, 2);
            $forensic->ai_percentage = round((($forensic->ai_injected_char_count + $forensic->pasted_char_count) / $total) * 100, 2);
        }

        $forensic->last_synced_at = now();
        $forensic->save();

        return response()->json([
            'status' => 'synced',
            'forensics' => [
                'human_percentage' => $forensic->human_percentage,
                'ai_percentage' => $forensic->ai_percentage,
            ]
        ]);
    }
}
