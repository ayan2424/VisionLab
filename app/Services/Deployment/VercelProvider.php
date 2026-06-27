<?php

namespace App\Services\Deployment;

use App\Contracts\DeploymentProvider;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Support\Facades\Http;

class VercelProvider implements DeploymentProvider
{
    private ?string $tokenOverride;

    public function __construct(?string $tokenOverride = null)
    {
        $this->tokenOverride = $tokenOverride;
    }

    public function deploy(Workspace $workspace): array
    {
        $user = $workspace->owner;
        $token = $this->tokenOverride ?: ($user?->vercel_token ?: config('visionlab.deploy.vercel_token'));

        if (!$token) {
            throw new \Exception('Vercel API token is not configured.');
        }

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

        if (empty($vercelFiles)) {
            throw new \Exception('No files found in workspace to deploy.');
        }

        $response = Http::withToken($token)
            ->timeout(60)
            ->post('https://api.vercel.com/v13/deployments', [
                'name'  => 'visionlab-' . $workspace->id,
                'files' => $vercelFiles,
                'projectSettings' => [
                    'framework' => null,
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Vercel API request failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'id'     => $data['id'] ?? '',
            'url'    => isset($data['url']) ? 'https://' . $data['url'] : '',
            'status' => 'deployed',
        ];
    }

    public function getStatus(string $deploymentId): string
    {
        // For production simplicity, we check if the ID is present.
        return $deploymentId ? 'ready' : 'failed';
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
