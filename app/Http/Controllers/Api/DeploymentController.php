<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeploymentController extends Controller
{
    /**
     * Zip and deploy a workspace to Vercel/Railway.
     */
    public function deploy(Request $request, Room $room)
    {
        // Must be the owner or authorized
        if ($room->owner_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized to deploy this workspace'], 403);
        }

        // Create deployment record
        $deployment = Deployment::create([
            'user_id' => auth()->id(),
            'workspace_id' => $room->id,
            'provider' => 'vercel',
            'project_name' => 'visionlab-'.$room->slug,
            'status' => 'pending',
        ]);

        // Mock deployment logic for Aptech Vision 2026 Demo
        // In a real scenario, we would zip /home/coder/project from Docker and POST to Vercel API.

        $projectName = 'visionlab-'.$room->slug.'-'.Str::random(5);
        $mockUrl = 'https://'.$projectName.'.vercel.app';

        // Simulate deployment time
        // Actually, we should return immediately and let a background job do it,
        // but for demo, returning a mock URL is fine.

        $deployment->update([
            'url' => $mockUrl,
            'status' => 'success',
        ]);

        return response()->json([
            'message' => 'Deployment successful',
            'url' => $mockUrl,
            'deployment' => $deployment,
        ]);
    }
}
