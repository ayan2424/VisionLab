<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class WebhookController extends Controller
{
    public function deploy(Request $request)
    {
        $secret = config('services.github.webhook_secret');
        
        if (!$secret) {
            Log::warning('GitHub webhook secret not configured.');
            return response()->json(['error' => 'Secret not configured'], 500);
        }

        $signature = $request->header('X-Hub-Signature-256');
        
        if (!$signature) {
            return response()->json(['error' => 'Signature missing'], 400);
        }

        $payload = $request->getContent();
        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($hash, $signature)) {
            Log::warning('GitHub webhook signature mismatch.');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Only deploy on push to main branch
        $branch = $request->input('ref');
        if ($branch !== 'refs/heads/main') {
            return response()->json(['message' => 'Ignoring push to non-main branch']);
        }

        Log::info('GitHub webhook verified. Starting deployment background task...');

        // Execute the deploy script in the background
        $scriptPath = base_path('deploy.sh');
        
        if (!file_exists($scriptPath)) {
            Log::error('Deployment script not found at: ' . $scriptPath);
            return response()->json(['error' => 'Deployment script not found'], 500);
        }

        // Run bash script in the background and discard output to prevent hanging
        exec("nohup bash {$scriptPath} > /dev/null 2>&1 &");

        return response()->json(['message' => 'Deployment triggered successfully']);
    }
}
