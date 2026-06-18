<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class JitsiService
{
    /**
     * Generate a Jitsi Meet JWT token for authenticated video sessions.
     */
    public function generateJwt(User $user, string $roomName): ?string
    {
        $appId = config('visionlab.jitsi.app_id');
        $secret = config('visionlab.jitsi.jwt_secret');

        if (!$appId || !$secret) {
            return null; // Fall back to public Jitsi if not configured
        }

        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'context' => [
                'user' => [
                    'avatar' => $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => (string) $user->id,
                ],
                'features' => [
                    'livestreaming' => false,
                    'recording' => false,
                ]
            ],
            'aud' => 'jitsi',
            'iss' => $appId,
            'sub' => $appId,
            'room' => $roomName,
            'exp' => now()->addHours(4)->timestamp,
            'nbf' => now()->subMinutes(5)->timestamp,
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
