<?php

namespace App\Http\Controllers;

use App\Models\Deployment;
use App\Models\Workspace;
use App\Jobs\DeployWorkspaceJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Check if student has configured personal credentials
        $token = $provider === 'vercel' ? $user->vercel_token : $user->railway_token;
        if (!$token && !config("visionlab.deploy.{$provider}_token")) {
            return response()->json([
                'error' => "You must configure your " . ucfirst($provider) . " API token in your settings before deploying.",
            ], 422);
        }

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

        // Create deployment record
        $deployment = Deployment::create([
            'workspace_id'  => $workspace->id,
            'user_id'       => $user->id,
            'provider'      => $provider,
            'status'        => 'queued',
            'public_url'    => null,
            'error_summary' => null,
            'job_metadata'  => ['name' => $request->input('name', 'visionlab-' . $workspace->id)],
        ]);

        try {
            // Dispatch background deployment job
            DeployWorkspaceJob::dispatch($deployment->id);

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
}
