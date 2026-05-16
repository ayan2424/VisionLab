<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Services\CodeServerManager;
use Illuminate\Console\Command;

class CleanupWorkspaces extends Command
{
    protected $signature = 'workspaces:cleanup
                            {--force : Force stop all running containers}
                            {--inactive=24 : Stop containers inactive for N hours}';

    protected $description = 'Stop and remove inactive workspace containers to free resources';

    public function handle(CodeServerManager $manager): int
    {
        $hours = (int) $this->option('inactive');
        $force = $this->option('force');

        $this->info("🧹 VisionLab Workspace Cleanup");
        $this->info($force ? "   Mode: FORCE (stopping all)" : "   Mode: Inactive > {$hours} hours");

        $rooms = Room::all();
        $stopped = 0;

        foreach ($rooms as $room) {
            $status = $manager->getStatus($room);

            if (!$status['running']) continue;

            if ($force) {
                $this->warn("   ⏹  Stopping: {$room->slug}");
                $manager->stopWorkspace($room);
                $stopped++;
                continue;
            }

            // Check last access time (if no activity for $hours)
            $lastAccess = $room->updated_at;
            if ($lastAccess && $lastAccess->diffInHours(now()) >= $hours) {
                $this->warn("   ⏹  Stopping (inactive {$lastAccess->diffInHours(now())}h): {$room->slug}");
                $manager->stopWorkspace($room);
                $stopped++;
            }
        }

        $this->info("✅ Done. Stopped {$stopped} container(s).");

        return self::SUCCESS;
    }
}
