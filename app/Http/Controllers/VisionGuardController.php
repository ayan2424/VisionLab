<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class VisionGuardController extends Controller
{
    /**
     * Record a telemetry event from the IDE extension or frontend.
     */
    public function logEvent(Request $request)
    {
        $user = Auth::user();

        // Rate Limit: 120 events per minute per user to prevent abuse
        $key = 'visionguard:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 120)) {
            return response()->json(['error' => 'Too Many Requests'], 429);
        }
        RateLimiter::hit($key, 60);

        $validator = Validator::make($request->all(), [
            'event_type'    => 'required|string|max:255',
            'event_data'    => 'nullable|array',
            'resource_type' => 'nullable|string|max:255',
            'resource_id'   => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $event = AnalyticsEvent::track(
            eventType: $request->input('event_type'),
            data: $request->input('event_data', []),
            userId: $user->id,
            resourceType: $request->input('resource_type'),
            resourceId: $request->input('resource_id')
        );

        return response()->json(['status' => 'logged', 'id' => $event->id]);
    }

    /**
     * Get forensics data for a specific user. (Admin only)
     */
    public function getForensics(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $targetUserId = $request->query('user_id');
        $validator = Validator::make($request->query(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $events = AnalyticsEvent::where('user_id', $targetUserId)
            ->whereIn('event_type', ['workspace.keystroke', 'workspace.paste', 'ai.patch_applied'])
            ->latest()
            ->take(100)
            ->get();

        // Calculate confidence score based on ratio of manual keystrokes to pastes/AI patches
        $keystrokes = $events->where('event_type', 'workspace.keystroke')->count();
        $pastes = $events->where('event_type', 'workspace.paste')->count();
        $aiPatches = $events->where('event_type', 'ai.patch_applied')->count();

        $totalEvents = $keystrokes + $pastes + $aiPatches;
        $confidenceScore = $totalEvents > 0 
            ? round(($keystrokes / $totalEvents) * 100) 
            : 100;

        return response()->json([
            'confidence_score' => $confidenceScore,
            'recent_events' => $events,
            'summary' => [
                'keystrokes' => $keystrokes,
                'pastes' => $pastes,
                'ai_patches' => $aiPatches,
            ]
        ]);
    }
}
