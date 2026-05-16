<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * POST /api/push/subscribe
     * Save a push subscription for the authenticated user.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint'         => 'required|url|max:500',
            'keys.p256dh'      => 'required|string',
            'keys.auth'        => 'required|string',
            'content_encoding' => 'sometimes|string|max:20',
        ]);

        PushSubscription::updateOrCreate(
            [
                'user_id'  => Auth::id(),
                'endpoint' => $request->endpoint,
            ],
            [
                'public_key'       => $request->input('keys.p256dh'),
                'auth_token'       => $request->input('keys.auth'),
                'content_encoding' => $request->input('content_encoding', 'aesgcm'),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Push subscription saved.']);
    }

    /**
     * POST /api/push/unsubscribe
     * Remove push subscription.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Push subscription removed.']);
    }

    /**
     * GET /api/push/vapid-public-key
     * Return the VAPID public key for client-side subscription.
     */
    public function vapidPublicKey(): JsonResponse
    {
        return response()->json([
            'public_key' => config('webpush.public_key', env('VAPID_PUBLIC_KEY', '')),
        ]);
    }
}
