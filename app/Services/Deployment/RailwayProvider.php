<?php

namespace App\Services\Deployment;

use App\Contracts\DeploymentProvider;
use App\Models\Workspace;
use Illuminate\Support\Facades\Http;

class RailwayProvider implements DeploymentProvider
{
    private ?string $tokenOverride;

    public function __construct(?string $tokenOverride = null)
    {
        $this->tokenOverride = $tokenOverride;
    }

    public function deploy(Workspace $workspace): array
    {
        $user = $workspace->owner;
        $token = $this->tokenOverride ?: ($user?->railway_token ?: config('visionlab.deploy.railway_token'));

        if (!$token) {
            throw new \Exception('Railway API token is not configured.');
        }

        $response = Http::withToken($token)
            ->timeout(60)
            ->post('https://backboard.railway.app/graphql/v2', [
                'query' => 'mutation { deploymentCreate(input: { projectId: "visionlab" }) { id status } }',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Railway API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        // Handle GraphQL errors
        if (isset($data['errors'])) {
            throw new \Exception('Railway GraphQL Error: ' . json_encode($data['errors']));
        }

        $deployId = $data['data']['deploymentCreate']['id'] ?? '';
        $url = $deployId ? "https://{$deployId}.up.railway.app" : '';

        return [
            'id'     => $deployId,
            'url'    => $url,
            'status' => 'deployed',
        ];
    }

    public function getStatus(string $deploymentId): string
    {
        return $deploymentId ? 'ready' : 'failed';
    }
}
