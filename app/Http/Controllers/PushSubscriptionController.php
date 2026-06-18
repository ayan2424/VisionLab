<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = $request->user();

        $subscription = \App\Models\PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id' => $user->id,
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                'browser_info' => $request->header('User-Agent'),
                'revoked_at' => null,
            ]
        );

        return response()->json(['success' => true, 'id' => $subscription->id]);
    }
}
