<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\VideoRoom;
use App\Models\VideoAttendance;
use App\Models\AiPendingPatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * VideoRoomController — Jitsi Meet integration for workspace video calls.
 *
 * Supports both self-hosted Jitsi and JaaS (Jitsi as a Service).
 * Generates JWT tokens for authenticated access and broadcasts
 * call events via Laravel Reverb.
 */
class VideoRoomController extends Controller
{
    /**
     * POST /api/workspace/{slug}/video/start
     * Start or join an active video call.
     */
    public function start(Request $request, string $slug): JsonResponse
    {
        $room = Room::where('slug', $slug)->first();
        if (!$room) return response()->json(['error' => 'Workspace not found'], 404);

        $user = Auth::user();

        // Check if a call is already active
        $activeCall = cache()->get("video_call:{$slug}");
        if ($activeCall) {
            $videoRoom = VideoRoom::where('room_name', $activeCall['room_name'])->first();
            return response()->json([
                'active'       => true,
                'room_name'    => $activeCall['room_name'],
                'jitsi_domain' => $activeCall['jitsi_domain'],
                'jwt'          => $this->generateJwt($user, $activeCall['room_name']),
                'starter'      => $activeCall['starter'],
                'video_room_id'=> $videoRoom ? $videoRoom->id : null,
            ]);
        }

        // Create a new video room
        $roomName    = 'visioncode-' . $slug . '-' . Str::random(6);
        $jitsiDomain = config('visionlab.jitsi.domain', 'meet.jit.si');

        // Create DB Record
        $videoRoom = VideoRoom::create([
            'course_id'    => $room->course_id ?? null,
            'workspace_id' => $room->workspace_id ?? null,
            'host_id'      => $user->id,
            'title'        => "Live Session: {$room->name}",
            'room_name'    => $roomName,
            'started_at'   => now(),
            'is_active'    => true,
        ]);

        $callData = [
            'room_name'    => $roomName,
            'jitsi_domain' => $jitsiDomain,
            'starter'      => ['id' => $user->id, 'name' => $user->name],
            'started_at'   => now()->toISOString(),
            'video_room_id'=> $videoRoom->id,
        ];

        // Cache the active call (expires in 4 hours)
        cache()->put("video_call:{$slug}", $callData, now()->addHours(4));

        // Broadcast VideoCallStarted event
        try {
            broadcast(new \App\Events\VideoCallStarted(
                $slug,
                $roomName,
                $jitsiDomain,
                $user->id,
                $user->name
            ))->toOthers();
        } catch (\Exception $e) {
            // Reverb may not be running in dev
        }

        return response()->json([
            'active'       => true,
            'room_name'    => $roomName,
            'jitsi_domain' => $jitsiDomain,
            'jwt'          => $this->generateJwt($user, $roomName),
            'starter'      => $callData['starter'],
            'video_room_id'=> $videoRoom->id,
        ]);
    }

    /**
     * GET /api/workspace/{slug}/video/status
     * Check if a video call is active.
     */
    public function status(string $slug): JsonResponse
    {
        $activeCall = cache()->get("video_call:{$slug}");

        return response()->json([
            'active'  => $activeCall !== null,
            'call'    => $activeCall,
        ]);
    }

    /**
     * POST /api/workspace/{slug}/video/attendance
     * Track attendance for an active video call.
     */
    public function attendance(Request $request, string $slug): JsonResponse
    {
        $activeCall = cache()->get("video_call:{$slug}");
        if (!$activeCall) {
            return response()->json(['error' => 'No active call found'], 404);
        }

        $videoRoomId = $activeCall['video_room_id'];
        $action = $request->input('action', 'join'); // 'join' or 'leave'
        $userId = Auth::id();

        if ($action === 'join') {
            VideoAttendance::updateOrCreate(
                ['video_room_id' => $videoRoomId, 'user_id' => $userId],
                ['joined_at' => now()]
            );
        } elseif ($action === 'leave') {
            $attendance = VideoAttendance::where('video_room_id', $videoRoomId)
                ->where('user_id', $userId)
                ->first();
            if ($attendance) {
                $attendance->update(['left_at' => now()]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * POST /api/workspace/{slug}/video/end
     * End the active video call.
     */
    public function end(Request $request, string $slug): JsonResponse
    {
        $user = Auth::user();

        // Only instructor/admin or the starter can end
        $activeCall = cache()->get("video_call:{$slug}");
        if (!$activeCall) {
            return response()->json(['error' => 'No active call found'], 404);
        }

        if (!$user->isAdmin() && !$user->isInstructor()) {
            if (($activeCall['starter']['id'] ?? null) !== $user->id) {
                return response()->json(['error' => 'Only the call starter or an instructor can end the call'], 403);
            }
        }

        // Close room in DB
        $videoRoom = VideoRoom::find($activeCall['video_room_id']);
        if ($videoRoom) {
            $videoRoom->update([
                'ended_at' => now(),
                'is_active' => false,
            ]);

            // Phase 7: AI Meeting Notes Generation
            // We simulate meeting notes based on collaborative chat messages that occurred during the meeting.
            $this->generateAiMeetingNotes($videoRoom, $slug);
        }

        cache()->forget("video_call:{$slug}");

        try {
            broadcast(new \App\Events\VideoCallEnded($slug))->toOthers();
        } catch (\Exception $e) {
            // Reverb may not be running
        }

        return response()->json(['success' => true]);
    }

    /**
     * Generate AI Meeting Notes based on collab chat messages.
     */
    private function generateAiMeetingNotes(VideoRoom $videoRoom, string $slug)
    {
        $messages = \App\Models\ChatMessage::with('user')
            ->where('workspace_id', $videoRoom->workspace_id)
            ->where('created_at', '>=', $videoRoom->started_at)
            ->where('created_at', '<=', $videoRoom->ended_at ?: now())
            ->get();

        if ($messages->isEmpty()) {
            $notes = "### AI Meeting Summary\n\n";
            $notes .= "- **Room**: {$videoRoom->title}\n";
            $notes .= "- **Duration**: " . $videoRoom->started_at->diffInMinutes($videoRoom->ended_at ?: now()) . " minutes\n";
            $notes .= "- **Chat Activity**: No messages exchanged.\n\n";
            $notes .= "*(AI Note: The collaborative session had no chat messages exchanged. No direct meeting summary could be generated.)*";
            $videoRoom->update(['meeting_notes' => $notes]);
            return;
        }

        $transcript = "";
        foreach ($messages as $msg) {
            $userName = $msg->user ? $msg->user->name : 'Unknown User';
            $transcript .= "{$userName}: {$msg->message}\n";
        }

        $systemPrompt = "You are an expert AI meeting assistant for VisionLab. Summarize the following chat log of a live collaborative pair-programming session. Focus on what topics were discussed, what code was written/edited, what bugs were resolved, and any action items. Make the summary structured, professional, and concise in Markdown format under a '### AI Meeting Summary' header.";

        $aiService = app(\App\Services\AiService::class);
        $summary = $aiService->getDirectCompletion($systemPrompt, $transcript);

        $videoRoom->update(['meeting_notes' => $summary]);
    }

    /**
     * Generate a simple JWT for Jitsi authentication.
     * For self-hosted Jitsi with JWT auth enabled.
     */
    private function generateJwt($user, string $roomName): ?string
    {
        $appId     = config('visionlab.jitsi.app_id');
        $appSecret = config('visionlab.jitsi.jwt_secret');

        if (!$appId || !$appSecret) {
            return null; // No JWT auth configured, use public Jitsi
        }

        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $now    = time();

        $payload = base64_encode(json_encode([
            'iss' => $appId,
            'sub' => config('visionlab.jitsi.domain', 'meet.jit.si'),
            'aud' => $appId,
            'iat' => $now,
            'exp' => $now + 7200, // 2 hours
            'nbf' => $now - 10,
            'room'    => $roomName,
            'context' => [
                'user' => [
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'id'     => (string) $user->id,
                    'avatar' => $user->avatar_url ?? '',
                ],
            ],
            'moderator' => $user->isInstructor() || $user->isAdmin(),
        ]));

        $signature = hash_hmac('sha256', "{$header}.{$payload}", $appSecret, true);
        $signature = base64_encode($signature);

        // URL-safe base64
        $header    = strtr($header, '+/', '-_');
        $payload   = strtr($payload, '+/', '-_');
        $signature = strtr($signature, '+/', '-_');

        return "{$header}.{$payload}.{$signature}";
    }
}
