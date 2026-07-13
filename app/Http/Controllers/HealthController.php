<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Exception;

class HealthController extends Controller
{
    /**
     * Perform 6 dependency probes for production observability.
     * Returns machine-readable JSON — used by uptime monitors & /healthz endpoint.
     */
    public function check(): JsonResponse
    {
        [$checks, $status] = $this->runProbes();

        // If any critical infrastructure is down, set HTTP status to 503
        $httpCode = ($checks['database'] && $checks['redis']) ? 200 : 503;

        return response()->json([
            'status'    => $status,
            'timestamp' => now()->toIso8601String(),
            'checks'    => $checks,
        ], $httpCode);
    }

    /**
     * Returns the human-readable system status page.
     * Uses the same 6 probes as check() but renders a Blade view.
     */
    public function status(): View
    {
        [$checks, $status] = $this->runProbes();

        return view('admin.status', compact('checks', 'status'));
    }

    /**
     * Shared probe runner — performs all 6 infrastructure checks.
     * Isolated here to prevent duplication between JSON & Blade responses.
     *
     * @return array{0: array<string, bool>, 1: string}
     */
    private function runProbes(): array
    {
        $checks = [
            'database'    => false,
            'redis'       => false,
            'reverb'      => false,
            'storage'     => false,
            'ai_config'   => false,
            'jitsi_config'=> false,
        ];

        $status = 'operational';

        // 1. Database Probe
        try {
            DB::connection()->getPdo();
            $checks['database'] = true;
        } catch (\Throwable) {
            $status = 'degraded';
        }

        // 2. Redis Probe
        try {
            Redis::connection()->ping();
            $checks['redis'] = true;
        } catch (\Throwable) {
            $status = 'degraded';
        }

        // 3. Reverb Probe (Check if broadcast driver is reverb)
        try {
            $checks['reverb'] = config('broadcasting.default') === 'reverb';
        } catch (\Throwable) {
            $status = 'degraded';
        }

        // 4. Storage Probe
        try {
            $checks['storage'] = Storage::disk('local')->put('.health', 'ok');
            Storage::disk('local')->delete('.health');
        } catch (\Throwable) {
            $status = 'degraded';
        }

        // 5. AI Config Probe
        $checks['ai_config'] = !empty(config('visionlab.ai.provider')) && !empty(config('visionlab.ai.api_key'));

        // 6. Jitsi Config Probe
        $checks['jitsi_config'] = !empty(config('visionlab.jitsi.app_id'));

        return [$checks, $status];
    }
}
