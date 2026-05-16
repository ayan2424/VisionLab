<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionForensic;
use Illuminate\Http\Request;

class ForensicsController extends Controller
{
    /**
     * Receive telemetry from the VS Code extension.
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'human_keystrokes' => 'integer',
            'ai_injected_chars' => 'integer',
            'time_spent_seconds' => 'integer',
        ]);

        $submission = Submission::findOrFail($validated['submission_id']);

        // Ensure user is the owner
        if ($submission->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $forensic = SubmissionForensic::firstOrCreate(
            ['submission_id' => $submission->id]
        );

        $forensic->increment('human_keystrokes', $validated['human_keystrokes'] ?? 0);
        $forensic->increment('ai_injected_chars', $validated['ai_injected_chars'] ?? 0);
        $forensic->increment('time_spent_seconds', $validated['time_spent_seconds'] ?? 0);

        return response()->json(['status' => 'synced']);
    }
}
