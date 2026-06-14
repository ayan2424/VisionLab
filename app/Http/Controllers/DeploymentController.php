<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * DeploymentController — One-click cloud deployment for student workspaces.
 *
 * Supports Vercel and Railway deployment targets.
 * Each deployment is tracked with full lifecycle status in the database.
 */
class DeploymentController extends Controller
{
    /** POST /workspace/{workspace}/deploy — Deploy workspace to cloud */
    public function deploy(Workspace $workspace, Request $request): JsonResponse
    {
        $this->authorize('access', $workspace);

        $request->validate([
            'provider' => 'required|in:vercel,railway',
            'name'     => 'nullable|string|max:50',
        ]);

        $user     = Auth::user();
        $provider = $request->input('provider');

        // Check for active deployment
        $existing = Deployment::where('workspace_id', $workspace->id)
            ->where('provider', $provider)
            ->whereIn('status', ['queued', 'building'])
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'A deployment is already in progress.',
                'deployment' => $existing,
            ], 409);
        }

        // Create deployment record (fields match Deployment model $fillable)
        $deployment = Deployment::create([
            'workspace_id'  => $workspace->id,
            'user_id'       => $user->id,
            'provider'      => $provider,
            'status'        => 'queued',
            'public_url'    => null,
            'error_summary' => null,
            'job_metadata'  => ['name' => $request->input('name', 'visionlab-' . $workspace->id)],
        ]);

        // Dispatch the deployment (async via queue in production)
        try {
            match ($provider) {
                'vercel'  => $this->deployToVercel($workspace, $deployment),
                'railway' => $this->deployToRailway($workspace, $deployment),
            };

            return response()->json([
                'status'     => 'queued',
                'deployment' => $deployment->fresh(),
            ]);
        } catch (\Exception $e) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => $e->getMessage(),
            ]);

            return response()->json([
                'error'  => 'Deployment failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** GET /workspace/{workspace}/deployments — List deployments */
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $deployments = Deployment::where('workspace_id', $workspace->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json($deployments);
    }

    /** GET /deployments/{deployment}/status — Check deployment status */
    public function status(Deployment $deployment): JsonResponse
    {
        return response()->json($deployment);
    }

    /** DELETE /deployments/{deployment} — Cancel/delete deployment */
    public function destroy(Deployment $deployment): JsonResponse
    {
        $user = Auth::user();
        if ($deployment->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $deployment->update(['status' => 'failed', 'error_summary' => 'Cancelled by user']);

        return response()->json(['status' => 'cancelled']);
    }

    // ══════════════════════════════════════════════════════════════════════
    // Provider-specific deployment logic
    // ══════════════════════════════════════════════════════════════════════

    private function deployToVercel(Workspace $workspace, Deployment $deployment): void
    {
        $token = config('visionlab.deploy.vercel_token');

        if (!$token) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => 'Vercel token not configured.',
            ]);
            return;
        }

        $deployment->update(['status' => 'building']);

        // Prepare deployment payload
        $csm = app(CodeServerManager::class);
        $files = $csm->listFiles($workspace);

        $vercelFiles = [];
        foreach ($this->flattenFiles($files) as $file) {
            $content = $csm->readFile($workspace, $file['path']);
            if ($content !== null) {
                $vercelFiles[] = [
                    'file'     => $file['path'],
                    'data'     => base64_encode($content),
                    'encoding' => 'base64',
                ];
            }
        }

        try {
            $response = Http::withToken($token)
                ->timeout(60)
                ->post('https://api.vercel.com/v13/deployments', [
                    'name'  => $deployment->job_metadata['name'] ?? 'visionlab-deploy',
                    'files' => $vercelFiles,
                    'projectSettings' => [
                        'framework' => null,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $deployment->update([
                    'status'        => 'deployed',
                    'deployment_id' => $data['id'] ?? null,
                    'public_url'    => $data['url'] ?? null,
                    'deployed_at'   => now(),
                    'job_metadata'  => array_merge($deployment->job_metadata ?? [], [
                        'vercel_response' => $data,
                    ]),
                ]);
            } else {
                $deployment->update([
                    'status'        => 'failed',
                    'error_summary' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => $e->getMessage(),
            ]);
        }
    }

    private function deployToRailway(Workspace $workspace, Deployment $deployment): void
    {
        $token = config('visionlab.deploy.railway_token');

        if (!$token) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => 'Railway token not configured.',
            ]);
            return;
        }

        $deployment->update(['status' => 'building']);

        try {
            $response = Http::withToken($token)
                ->timeout(60)
                ->post('https://backboard.railway.app/graphql/v2', [
                    'query' => 'mutation { deploymentCreate(input: { projectId: "visionlab" }) { id status } }',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $deployId = $data['data']['deploymentCreate']['id'] ?? null;
                $deployment->update([
                    'status'        => 'deployed',
                    'deployment_id' => $deployId,
                    'public_url'    => "https://{$deployId}.railway.app",
                    'deployed_at'   => now(),
                    'job_metadata'  => array_merge($deployment->job_metadata ?? [], [
                        'railway_response' => $data,
                    ]),
                ]);
            } else {
                $deployment->update([
                    'status'        => 'failed',
                    'error_summary' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => $e->getMessage(),
            ]);
        }
    }

    /** Flatten nested file tree from listFiles into a flat array */
    private function flattenFiles(array $tree, string $prefix = ''): array
    {
        $result = [];
        foreach ($tree as $entry) {
            $path = $prefix ? $prefix . '/' . $entry['name'] : $entry['name'];
            if ($entry['type'] === 'file') {
                $result[] = ['name' => $entry['name'], 'path' => $path];
            } elseif (isset($entry['children'])) {
                $result = array_merge($result, $this->flattenFiles($entry['children'], $path));
            }
        }
        return $result;
    }
}
