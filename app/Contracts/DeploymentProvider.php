<?php

namespace App\Contracts;

use App\Models\Workspace;

interface DeploymentProvider
{
    /**
     * Deploy a workspace and return the provider-specific deployment ID or URL.
     *
     * @param Workspace $workspace
     * @return array{id: string, url: string, status: string}
     */
    public function deploy(Workspace $workspace): array;

    /**
     * Check the status of a deployment.
     *
     * @param string $deploymentId
     * @return string
     */
    public function getStatus(string $deploymentId): string;
}
