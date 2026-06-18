<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Exception;

class HealthController extends Controller
{
    /**
     * Perform 6 dependency probes for production observability.
     */
    public function check(): JsonResponse
    {
        $checks = [
            'database' => false,
            'redis' => false,
            'reverb' => false,
            'storage' => false,
            'ai_config' => false,
            'jitsi_config' => false,
        ];
        
        $status = 'ok';

        // 1. Database Probe
        try {
            DB::connection()->getPdo();
            $checks['database'] = true;
        } catch (Exception $e) {
            $status = 'degraded';
        }

        // 2. Redis Probe
        try {
            Redis::connection()->ping();
            $checks['redis'] = true;
        } catch (Exception $e) {
            $status = 'degraded';
        }

        // 3. Reverb Probe (Check if broadcast driver is reverb)
        try {
            $checks['reverb'] = config('broadcasting.default') === 'reverb';
        } catch (Exception $e) {
            $status = 'degraded';
        }

        // 4. Storage Probe
        try {
            $checks['storage'] = Storage::disk('local')->put('.health', 'ok');
            Storage::disk('local')->delete('.health');
        } catch (Exception $e) {
            $status = 'degraded';
        }

        // 5. AI Config Probe
        $checks['ai_config'] = !empty(config('visionlab.ai.provider')) && !empty(config('visionlab.ai.api_key'));

        // 6. Jitsi Config Probe
        $checks['jitsi_config'] = !empty(config('visionlab.jitsi.app_id'));

        // If any critical infrastructure is down, set HTTP status to 503
        $httpCode = ($checks['database'] && $checks['redis']) ? 200 : 503;

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $httpCode);
    }
}
