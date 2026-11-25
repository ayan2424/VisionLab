<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionForensic;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForensicsController extends Controller
{
    /**
     * POST /api/workspace/{slug}/forensics/sync
     * Receives telemetry from the VS Code extension about human vs AI contribution.
     */
    public function sync(string $slug, Request $request): JsonResponse
    {
        $stripped = str_starts_with($slug, 'ws-') ? substr($slug, 3) : $slug;

        $workspace = ctype_digit($stripped)
            ? (Workspace::find((int) $stripped) ?? Workspace::where('slug', $slug)->firstOrFail())
            : Workspace::where('slug', $slug)->firstOrFail();

        $this->authorize('access', $workspace);

        $validated = $request->validate([
            'humanKeystrokeDelta' => 'required|integer|min:0',
            'aiInjectedCharDelta' => 'required|integer|min:0',
            'pastedCharDelta'     => 'sometimes|integer|min:0',
            'pasteEventDelta'     => 'sometimes|integer|min:0',
            'focusLossDelta'      => 'sometimes|integer|min:0',
            'idleSecondsDelta'    => 'sometimes|integer|min:0',
        ]);

        if (!$workspace->course_id) {
            return response()->json(['status' => 'ignored', 'reason' => 'No course linked to workspace']);
        }

        $submission = Submission::where('student_id', $workspace->student_id)
            ->whereHas('assignment', function ($q) use ($workspace) {
                $q->where('course_id', $workspace->course_id);
            })
            ->whereIn('status', ['not_started', 'in_progress'])
            ->latest()
            ->first();

        if (!$submission) {
            return response()->json(['status' => 'ignored', 'reason' => 'No active submission found']);
        }

        // Find or create the forensic record using actual submission_forensics columns
        $forensic = SubmissionForensic::firstOrCreate(
            ['submission_id' => $submission->id],
            [
                'workspace_id'       => $workspace->id,
                'human_keystrokes'   => 0,
                'ai_patches_applied' => 0,
                'pasted_count'       => 0,
                'imported_count'     => 0,
                'human_pct'          => 100.00,
                'ai_pct'             => 0.00,
                'confidence'         => 'high',
                'keystroke_count'    => 0,
                'paste_event_count'  => 0,
                'paste_char_total'   => 0,
                'focus_loss_count'   => 0,
                'total_idle_seconds' => 0,
            ]
        );

        $forensic->workspace_id = $workspace->id;
        $forensic->human_keystrokes += (int) $validated['humanKeystrokeDelta'];
        $forensic->keystroke_count += (int) $validated['humanKeystrokeDelta'];
        $forensic->ai_patches_applied += (int) $validated['aiInjectedCharDelta'];
        $forensic->pasted_count += (int) ($validated['pastedCharDelta'] ?? 0);
        $forensic->paste_char_total += (int) ($validated['pastedCharDelta'] ?? 0);
        $forensic->paste_event_count += (int) ($validated['pasteEventDelta'] ?? 0);
        $forensic->focus_loss_count += (int) ($validated['focusLossDelta'] ?? 0);
        $forensic->total_idle_seconds += (int) ($validated['idleSecondsDelta'] ?? 0);

        $total = $forensic->human_keystrokes
            + $forensic->ai_patches_applied
            + $forensic->pasted_count
            + $forensic->imported_count;

        if ($total > 0) {
            $forensic->human_pct = round(($forensic->human_keystrokes / $total) * 100, 2);
            $forensic->ai_pct = round((($forensic->ai_patches_applied + $forensic->pasted_count) / $total) * 100, 2);
            $forensic->confidence = $total >= 50 ? 'high' : ($total >= 10 ? 'medium' : 'low');
        }

        $forensic->last_synced_at = now();
        $forensic->save();

        return response()->json([
            'status' => 'synced',
            'forensics' => [
                'human_pct' => (float) $forensic->human_pct,
                'ai_pct'    => (float) $forensic->ai_pct,
                'confidence' => $forensic->confidence,
            ],
        ]);
    }
}
