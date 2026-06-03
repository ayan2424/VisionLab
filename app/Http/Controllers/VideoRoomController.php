<?php

namespace App\Http\Controllers;

use App\Events\VideoCallEnded;
use App\Events\VideoCallStarted;
use App\Models\Room;
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
        if (! $room) {
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        $user = Auth::user();

        // Check if a call is already active
        $activeCall = cache()->get("video_call:{$slug}");
        if ($activeCall) {
            return response()->json([
                'active' => true,
                'room_name' => $activeCall['room_name'],
                'jitsi_domain' => $activeCall['jitsi_domain'],
                'jwt' => $this->generateJwt($user, $activeCall['room_name']),
                'starter' => $activeCall['starter'],
            ]);
        }

        // Create a new video room
        $roomName = 'VisionLab-'.$slug.'-'.Str::random(6);
        $jitsiDomain = env('JITSI_DOMAIN', 'meet.jit.si');

        $callData = [
            'room_name' => $roomName,
            'jitsi_domain' => $jitsiDomain,
            'starter' => ['id' => $user->id, 'name' => $user->name],
            'started_at' => now()->toISOString(),
        ];

        // Cache the active call (expires in 4 hours)
        cache()->put("video_call:{$slug}", $callData, now()->addHours(4));

        // Broadcast VideoCallStarted event
        try {
            broadcast(new VideoCallStarted(
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
            'active' => true,
            'room_name' => $roomName,
            'jitsi_domain' => $jitsiDomain,
            'jwt' => $this->generateJwt($user, $roomName),
            'starter' => $callData['starter'],
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
            'active' => $activeCall !== null,
            'call' => $activeCall,
        ]);
    }

    /**
     * POST /api/workspace/{slug}/video/end
     * End the active video call.
     */
    public function end(Request $request, string $slug): JsonResponse
    {
        $user = Auth::user();

        // Only instructor/admin or the starter can end
        if (! $user->isAdmin() && ! $user->isInstructor()) {
            $activeCall = cache()->get("video_call:{$slug}");
            if ($activeCall && ($activeCall['starter']['id'] ?? null) !== $user->id) {
                return response()->json(['error' => 'Only the call starter or an instructor can end the call'], 403);
            }
        }

        cache()->forget("video_call:{$slug}");

        try {
            broadcast(new VideoCallEnded($slug))->toOthers();
        } catch (\Exception $e) {
            // Reverb may not be running
        }

        return response()->json(['success' => true]);
    }

    /**
     * Generate a simple JWT for Jitsi authentication.
     * For self-hosted Jitsi with JWT auth enabled.
     */
    private function generateJwt($user, string $roomName): ?string
    {
        $appId = env('JITSI_APP_ID');
        $appSecret = env('JITSI_APP_SECRET');

        if (! $appId || ! $appSecret) {
            return null; // No JWT auth configured, use public Jitsi
        }

        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $now = time();

        $payload = base64_encode(json_encode([
            'iss' => $appId,
            'sub' => env('JITSI_DOMAIN', 'meet.jit.si'),
            'aud' => $appId,
            'iat' => $now,
            'exp' => $now + 7200, // 2 hours
            'nbf' => $now - 10,
            'room' => $roomName,
            'context' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => (string) $user->id,
                    'avatar' => $user->avatar_url ?? '',
                ],
            ],
            'moderator' => $user->isInstructor() || $user->isAdmin(),
        ]));

        $signature = hash_hmac('sha256', "{$header}.{$payload}", $appSecret, true);
        $signature = base64_encode($signature);

        // URL-safe base64
        $header = strtr($header, '+/', '-_');
        $payload = strtr($payload, '+/', '-_');
        $signature = strtr($signature, '+/', '-_');

        return "{$header}.{$payload}.{$signature}";
    }
}
