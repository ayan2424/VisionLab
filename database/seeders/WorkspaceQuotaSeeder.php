<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkspaceQuota;

class WorkspaceQuotaSeeder extends Seeder
{
    public function run(): void
    {
        WorkspaceQuota::updateOrCreate(
            ['scope' => 'student'],
            [
                'max_workspaces' => 2,
                'memory_mb' => 512,
                'cpu_shares' => 512,
                'disk_mb' => 1024,
                'timeout_minutes' => 60,
            ]
        );

        WorkspaceQuota::updateOrCreate(
            ['scope' => 'instructor'],
            [
                'max_workspaces' => 5,
                'memory_mb' => 1024,
                'cpu_shares' => 1024,
                'disk_mb' => 5120,
                'timeout_minutes' => 120,
            ]
        );

        WorkspaceQuota::updateOrCreate(
            ['scope' => 'admin'],
            [
                'max_workspaces' => 10,
                'memory_mb' => 2048,
                'cpu_shares' => 2048,
                'disk_mb' => 10240,
                'timeout_minutes' => 240,
            ]
        );
    }
}
