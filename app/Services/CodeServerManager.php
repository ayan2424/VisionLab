<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\WorkspaceQuota;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * CodeServerManager — Manages VisionLab IDE Docker containers per workspace.
 *
 * In development (Docker unavailable), returns a fallback URL pointing to
 * a locally running OpenVSCode Server or shows a placeholder UI.
 *
 * In production, spawns a Docker container per workspace using visionlab/workspace (VisionLab IDE).
 * Supports: resource quotas, extension injection, heartbeat, file I/O with
 * realpath-based path traversal protection.
 */
class CodeServerManager
{
    /** Port range for dynamically allocated VisionLab IDE instances */
    private const PORT_MIN = 9000;
    private const PORT_MAX = 9999;

    /** Docker image for VisionLab IDE */
    private const DOCKER_IMAGE = 'visionlab/workspace:latest';

    /** Static cache for Docker availability */
    private static ?bool $dockerAvailable = null;

    /**
     * Start a workspace container.
     *
     * @param Workspace $workspace The workspace to start
     * @return array{url: string, port: int, token: string, container_id: string|null}
     */
    public function startWorkspace(Workspace $workspace): array
    {
        // 0. Ensure volume exists and root directory has correct permissions BEFORE anything else
        $volumeName = "vl_ws_{$workspace->id}_data";
        $basePath = "/home/coder/{$workspace->slug}";

        $process = new Process([$this->dockerCmd(), 'volume', 'create', $volumeName]);
        $process->run();
        
        $chownCmd = [
            $this->dockerCmd(), 'run', '--rm', '-v', "{$volumeName}:{$basePath}", 'alpine', 'chown', '1000:1000', $basePath
        ];
        $process = new Process($chownCmd);
        $process->run();

        // 1. Seed the default files and configs into the Docker volume (if not already seeded).
        $this->ensureWorkspaceDirectory($workspace);
        // Development mode — Docker not available
        if (!$this->isDockerAvailable()) {
            Log::info("CodeServerManager: Docker unavailable, using dev fallback for workspace {$workspace->name}");
            return $this->devFallback($workspace);
        }

        // Check if container already running
        $existingStatus = $this->getStatus($workspace);
        if ($existingStatus['running']) {
            return [
                'url'          => $existingStatus['url'],
                'port'         => $existingStatus['port'],
                'token'        => $existingStatus['token'],
                'container_id' => $existingStatus['container_id'],
            ];
        }

        $port  = $this->allocatePort();
        $token = $workspace->token;
        if (!$token) {
            $token = Str::random(32);
            $workspace->update(['token' => $token]);
        }
        $name  = 'vl_ws_' . $workspace->id;

        // Remove any stale container with the same name
        $this->removeContainer($name);

        // Resolve resource quota
        $quota = $this->resolveQuota($workspace);

        $image = config('visionlab.container_image', 'visionlab/workspace:latest');
        
        $isLocalhost = app()->environment('local', 'testing') || (in_array(request()->getHost(), ['localhost', '127.0.0.1']) && request()->getPort() !== 80 && request()->getPort() !== 443);
        $proxyUrl = $isLocalhost 
            ? "http://127.0.0.1:{$port}/?folder=/home/coder/{$workspace->slug}" 
            : "/ide/{$port}/?folder=/home/coder/{$workspace->slug}";

        $authMode = 'none'; // Permanently disabled password auth as requested

        // Local setup of extensions path (still on host, mounted as ro)
        $extensionsPath = storage_path('app/extensions');
        if (!is_dir($extensionsPath)) {
            mkdir($extensionsPath, 0755, true);
        }

        $disableMarketplace = false;
        // Global override check
        $globalAllowMarketplace = (bool) \App\Models\SystemConfig::getVal('global_allow_marketplace', true);
        if (!$globalAllowMarketplace) {
            $disableMarketplace = true;
        } elseif ($workspace->course_id && $workspace->course) {
            $disableMarketplace = !$workspace->course->allow_marketplace;
        }

        $cmd = [
            $this->dockerCmd(), 'run', '-d', '--init',
            '--name', $name,
            '--cap-drop', 'ALL',
            '--tmpfs', '/tmp',
            '--tmpfs', '/run',
            '--network', 'visionlab-workspace-net',
            '-v', "vl_ws_{$workspace->id}_data:/home/coder/{$workspace->slug}",
            '-v', "{$extensionsPath}:/var/opt/extensions:ro",
            '-p', "{$port}:8080",
            '-e', "PASSWORD={$token}",
            '-e', "VISIONCODE_API_TOKEN=" . ($workspace->owner?->createToken('workspace')->plainTextToken ?? ''),
            '-e', "VISIONCODE_WORKSPACE_ID={$workspace->id}",
            '-e', "VISIONCODE_USER_ID=" . (auth()->id() ?? '0'),
            '-e', "VISIONCODE_USER_NAME=" . (auth()->user()?->name ?? 'Guest'),
            '-e', "VISIONCODE_API_URL=" . url('/api'),
            '-e', "OPENAI_API_BASE=" . url('/api/ai/v1'),
            '-e', "OPENAI_API_KEY=" . ($workspace->owner?->createToken('workspace')->plainTextToken ?? 'dummy_key'),
        ];

        if ($disableMarketplace) {
            $cmd[] = '-e';
            $cmd[] = 'EXTENSIONS_GALLERY={"serviceUrl":""}';
        }

        $cmd = array_merge($cmd, [
            '--restart', 'unless-stopped',
            '--memory', ($quota['memory_mb'] ?? 2048) . 'm',
            '--cpu-shares', (string) ($quota['cpu_shares'] ?? 1024),
            '--entrypoint', 'sh',
            $image,
            '-c',
            'if [ -f /home/coder/' . escapeshellarg($workspace->slug) . '/.vision/dev.nix ]; then ' .
            'export NIXPKGS_ALLOW_INSECURE=1; ' .
            'exec nix-shell /home/coder/' . escapeshellarg($workspace->slug) . '/.vision/dev.nix --run "/usr/bin/entrypoint.sh --auth ' . escapeshellarg($authMode) . ' --bind-addr 0.0.0.0:8080 --disable-telemetry --trusted-origins https://visionlab.ayan24.me /home/coder/' . escapeshellarg($workspace->slug) . '"; ' .
            'else ' .
            'exec /usr/bin/entrypoint.sh --auth ' . escapeshellarg($authMode) . ' --bind-addr 0.0.0.0:8080 --disable-telemetry --trusted-origins https://visionlab.ayan24.me /home/coder/' . escapeshellarg($workspace->slug) . '; ' .
            'fi'
        ]);

        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();        if (!$process->isSuccessful()) {
            Log::error("CodeServerManager: Failed to start container", [
                'workspace' => $workspace->id,
                'error'     => $process->getErrorOutput()
            ]);
            return false;
        }

        // Wait a brief moment for container to initialize before seeding
        usleep(500000); 

        // Update database with running container details
        $containerId = trim($process->getOutput());
        $workspace->update([
            'port'         => $port,
            'status'       => 'running',
            'container_id' => $containerId,
            'heartbeat_at' => now(),
        ]);


        // Enforce removal of GitHub Copilot to maintain VisionLab AI Agent sovereignty
        $purgeCopilotCmd = [
            'docker', 'exec', $containerId, 'sh', '-c', 
            'rm -rf /home/coder/.local/share/code-server/extensions/github.copilot*'
        ];
        (new Process($purgeCopilotCmd))->run();

        // Inject VisionLab Nix Watcher VS Code Extension
        $extDir = '/home/coder/.local/share/code-server/extensions/visionlab-nix-watcher';
        $pkgJson = json_encode([
            "name" => "visionlab-nix-watcher",
            "displayName" => "VisionLab Nix Watcher",
            "version" => "1.0.0",
            "engines" => ["vscode" => "^1.60.0"],
            "activationEvents" => ["*"],
            "main" => "./extension.js"
        ]);
        $domain = url('/');
        $slug = $workspace->slug;
        $extJs = <<<JS
const vscode = require('vscode');
function activate(context) {
    let rebuildPromptShown = false;
    const disposable = vscode.workspace.onDidSaveTextDocument((document) => {
        if (document.fileName.endsWith('.vision/dev.nix')) {
            if (rebuildPromptShown) return;
            rebuildPromptShown = true;
            vscode.window.showInformationMessage(
                'Environment configuration changed. Rebuild the environment to apply new packages.', 
                'Rebuild Environment'
            ).then(selection => {
                rebuildPromptShown = false;
                if (selection === 'Rebuild Environment') {
                    vscode.env.openExternal(vscode.Uri.parse('{$domain}/workspace/{$slug}/rebuild'));
                }
            });
        }
    });
    context.subscriptions.push(disposable);
}
exports.activate = activate;
JS;
        $injectExtCmd = [
            $this->dockerCmd(), 'exec', '-u', '1000', $containerId, 'sh', '-c', 
            "mkdir -p {$extDir} && echo " . escapeshellarg(base64_encode($pkgJson)) . " | base64 -d > {$extDir}/package.json && echo " . escapeshellarg(base64_encode($extJs)) . " | base64 -d > {$extDir}/extension.js"
        ];
        (new Process($injectExtCmd))->run();

        // Setup automatic Nix environment activation for the integrated terminal
        $bashrcCmd = [
            $this->dockerCmd(), 'exec', '-u', '1000', $containerId, 'sh', '-c', 
            'echo \'if [ -f .vision/dev.nix ] && [ -z "$IN_NIX_SHELL" ]; then export NIXPKGS_ALLOW_INSECURE=1; exec nix-shell .vision/dev.nix; fi\' >> /home/coder/.bashrc'
        ];
        (new Process($bashrcCmd))->run();

        // Run Nix bootstrap if it exists (Phase 3: Declarative Workspace Build)
        if ($workspace->template_id && $workspace->template && !empty($workspace->template->bootstrap_script)) {
            $bootstrapCmd = [
                $this->dockerCmd(), 'exec', '-u', '1000', $containerId, 'sh', '-c', 
                'cd /home/coder/' . escapeshellarg($workspace->slug) . ' && if [ -f .vision/bootstrap.sh ]; then dos2unix .vision/bootstrap.sh 2>/dev/null; if [ -f .vision/dev.nix ]; then export NIXPKGS_ALLOW_INSECURE=1; nix-shell .vision/dev.nix --command "sh .vision/bootstrap.sh"; else sh .vision/bootstrap.sh; fi; fi'
            ];
            $bootstrapProcess = new Process($bootstrapCmd);
            $bootstrapProcess->setTimeout(300); // 5 mins for heavy installs
            $bootstrapProcess->run();
            
            if (!$bootstrapProcess->isSuccessful()) {
                Log::warning("CodeServerManager: Nix bootstrap script failed", [
                    'workspace' => $workspace->id,
                    'error'     => $bootstrapProcess->getErrorOutput()
                ]);
            }
        }

        // Update workspace record
        $workspace->update([
            'container_id'    => $containerId,
            'port'            => $port,
            'token'           => $token,
            'status'          => 'running',
            'heartbeat_at'    => now(),
            'container_image' => $image,
            'proxy_url'       => $proxyUrl,
            'quota_data'      => $quota,
        ]);

        Log::info("CodeServerManager: Started container", [
            'workspace'    => $workspace->id,
            'container_id' => substr($containerId, 0, 12),
            'port'         => $port,
        ]);

        // Inject Configs and Extensions dynamically
        try {
            $this->setupWorkspaceExtensions($name, $workspace);
        } catch (\Throwable $e) {
            Log::warning("Could not setup workspace extensions (Phase 4 not migrated yet?): " . $e->getMessage());
        }

        return [
            'url'          => $proxyUrl,
            'port'         => $port,
            'token'        => $token,
            'container_id' => $containerId,
        ];
    }

    /**
     * Stop and remove a workspace container.
     */
    public function stopWorkspace(Workspace $workspace): bool
    {
        $name = 'vl_ws_' . $workspace->id;

        if (!$this->isDockerAvailable()) {
            return true;
        }

        $result = $this->removeContainer($name);

        if ($result) {
            $workspace->update(['status' => 'stopped', 'heartbeat_at' => null]);
        }

        return $result;
    }

    /**
     * Completely delete a workspace container and its persistent storage volume.
     */
    public function deleteWorkspace(Workspace $workspace): bool
    {
        // 1. Stop and remove container
        $this->stopWorkspace($workspace);

        // 2. Remove the Docker Native Volume
        $volumeName = "vl_ws_{$workspace->id}_data";
        
        if ($this->isDockerAvailable()) {
            $process = new Process([$this->dockerCmd(), 'volume', 'rm', '-f', $volumeName]);
            $process->run();
            
            if (!$process->isSuccessful()) {
                Log::error("CodeServerManager: Failed to delete Docker volume", [
                    'workspace' => $workspace->id,
                    'volume' => $volumeName,
                    'error' => $process->getErrorOutput()
                ]);
            } else {
                Log::info("CodeServerManager: Deleted Docker volume", ['workspace' => $workspace->id, 'volume' => $volumeName]);
            }
        }

        return true;
    }

    /**
     * Restart a workspace container.
     */
    public function restartWorkspace(Workspace $workspace): bool
    {
        $name = 'vl_ws_' . $workspace->id;

        if (!$this->isDockerAvailable()) {
            return false;
        }

        $process = new Process([$this->dockerCmd(), 'restart', $name]);
        $process->setTimeout(30);
        $process->run();

        if ($process->isSuccessful()) {
            $workspace->update(['status' => 'running', 'heartbeat_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Get the status of a workspace container.
     */
    public function getStatus(Workspace $workspace): array
    {
        $name = 'vl_ws_' . $workspace->id;

        if (!$this->isDockerAvailable()) {
            return ['running' => false, 'url' => null, 'port' => null, 'token' => null, 'container_id' => null];
        }

        $process = new Process([$this->dockerCmd(), 'inspect', '--format', '{{.State.Running}}:{{.Id}}', $name]);
        $process->setTimeout(10);
        $process->run();

        if (!$process->isSuccessful()) {
            return ['running' => false, 'url' => null, 'port' => null, 'token' => null, 'container_id' => null];
        }

        $output = trim($process->getOutput());
        [$running, $id] = explode(':', $output, 2);

        if ($running !== 'true') {
            return ['running' => false, 'url' => null, 'port' => null, 'token' => null, 'container_id' => $id];
        }

        // Get the mapped port
        $portProcess = new Process([$this->dockerCmd(), 'port', $name, '8080']);
        $portProcess->setTimeout(10);
        $portProcess->run();
        $portOutput = trim($portProcess->getOutput());
        preg_match('/:(\\d+)$/', $portOutput, $portMatch);
        $port = isset($portMatch[1]) ? (int)$portMatch[1] : null;

        $isLocalhost = app()->environment('local', 'testing') || (in_array(request()->getHost(), ['localhost', '127.0.0.1']) && request()->getPort() !== 80 && request()->getPort() !== 443);
        $iframeUrl = $isLocalhost 
            ? "http://127.0.0.1:{$port}/?folder=/home/coder/{$workspace->slug}" 
            : "/ide/{$port}/?folder=/home/coder/{$workspace->slug}";

        return [
            'running'      => true,
            'url'          => $port ? $iframeUrl : null,
            'port'         => $port,
            'token'        => $workspace->token,
            'container_id' => $id,
        ];
    }

    /**
     * Check container health and update heartbeat.
     */
    public function heartbeat(Workspace $workspace): bool
    {
        if (!$workspace->container_id || !$this->isDockerAvailable()) {
            return false;
        }

        $process = new Process([
            $this->dockerCmd(), 'inspect', '--format', '{{.State.Running}}', $workspace->container_id,
        ]);
        $process->setTimeout(10);
        $process->run();

        $isRunning = trim($process->getOutput()) === 'true';

        $workspace->update([
            'status'       => $isRunning ? 'running' : 'stopped',
            'heartbeat_at' => $isRunning ? now() : $workspace->heartbeat_at,
        ]);

        return $isRunning;
    }

    /**
     * Check if the VisionLab IDE HTTP interface is ready to accept connections.
     */
    public function isWorkspaceReady(Workspace $workspace): bool
    {
        if (!$this->isDockerAvailable()) {
            return false;
        }

        // PERMANENT FIX: If port is missing in DB but container is running, fetch it dynamically.
        if (!$workspace->port) {
            $status = $this->getStatus($workspace);
            if (!empty($status['port'])) {
                $workspace->update(['port' => $status['port']]);
            } else {
                return false;
            }
        }

        try {
            // Fast ping to the Docker bound port.
            // If visionlab-ide hasn't bound the internal 8080 port yet, this will fail or timeout.
            // Using 'localhost' instead of '127.0.0.1' for better Docker Desktop Windows compatibility
            $response = \Illuminate\Support\Facades\Http::timeout(2)
                ->get("http://127.0.0.1:{$workspace->port}/healthz");

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get container resource usage stats.
     */
    public function getStats(Workspace $workspace): ?array
    {
        if (!$workspace->container_id || !$this->isDockerAvailable()) {
            return null;
        }

        $process = new Process([
            $this->dockerCmd(), 'stats', '--no-stream', '--format', '{{json .}}', $workspace->container_id,
        ]);
        $process->setTimeout(10);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        return json_decode(trim($process->getOutput()), true);
    }

    // ══════════════════════════════════════════════════════════════════════
    // File I/O — Sandboxed Operations
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Helper to run commands against the workspace's Docker Volume.
     */
    private function executeDockerFileCommand(Workspace $workspace, array $command, ?string $stdin = null): Process
    {
        $containerName = 'vl_ws_' . $workspace->id;
        $volumeName = "vl_ws_{$workspace->id}_data";
        $basePath = '/home/coder/' . $workspace->slug;

        // If the container is running, execute directly inside it
        if ($workspace->container_id && $this->getStatus($workspace)['running']) {
            $baseCmd = [$this->dockerCmd(), 'exec', '-i', '-u', '1000', $containerName];
            
            // Handle directory commands like 'cd .'
            foreach ($command as $index => $arg) {
                if ($arg === '.') $command[$index] = $basePath;
                if (is_string($arg) && str_contains($arg, '/home/coder/' . $workspace->slug)) {
                    $command[$index] = str_replace('/home/coder/' . $workspace->slug, $basePath, $arg);
                }
            }

            $fullCmd = array_merge($baseCmd, $command);
        } else {
            // If container is STOPPED, spawn a temporary Alpine container mapped to the volume
            $baseCmd = [$this->dockerCmd(), 'run', '--rm', '-i', '--user', '1000:1000', '-v', "{$volumeName}:{$basePath}", 'alpine'];
            
            foreach ($command as $index => $arg) {
                if ($arg === '.') $command[$index] = $basePath;
                if (is_string($arg) && str_contains($arg, '/home/coder/' . $workspace->slug)) {
                    $command[$index] = str_replace('/home/coder/' . $workspace->slug, $basePath, $arg);
                }
            }
            
            $fullCmd = array_merge($baseCmd, $command);
        }
        
        $process = new Process($fullCmd);
        if ($stdin !== null) {
            $process->setInput($stdin);
        }
        $process->run();
        
        return $process;
    }

    /**
     * List files in a workspace directory (recursive tree).
     */
    public function listFiles(Workspace $workspace, string $relativePath = ''): array
    {
        $path = '/home/coder/' . $workspace->slug . ($relativePath ? '/' . ltrim($relativePath, '/') : '');
        $process = $this->executeDockerFileCommand($workspace, ['sh', '-c', "cd '$path' 2>/dev/null && find . -mindepth 1 -printf '%P|%y\\n'"]);

        if (!$process->isSuccessful()) {
            return [];
        }

        $output = trim($process->getOutput());
        if (empty($output)) return [];

        $lines = explode("\n", $output);
        $tree = [];
        
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) !== 2) continue;
            
            $filePath = $parts[0];
            $type = $parts[1] === 'd' ? 'directory' : 'file';
            
            // Skip hidden files except .visioncode_memory.md
            $basename = basename($filePath);
            if (str_starts_with($basename, '.') && $basename !== '.visioncode_memory.md') continue;
            
            // Build tree (simplified flat structure for now or construct nested array)
            // Note: The UI usually consumes a flat array or simple tree. We'll build a simplified flat structure with 'path' and 'name'.
            $tree[] = [
                'name' => basename($filePath),
                'path' => $filePath,
                'type' => $type,
            ];
        }

        // To properly build a nested tree from flat paths:
        $nestedTree = [];
        foreach ($tree as $item) {
            $pathParts = explode('/', $item['path']);
            if (count($pathParts) === 1) {
                $nestedTree[] = $item;
            } else {
                // For deep nested trees, this would need a proper tree builder.
                // Keeping it flat is often sufficient for basic Vercel deployments, which flatten it anyway.
            }
        }
        
        // Full nested tree builder:
        $buildTree = function(array &$elements, $parentId = 0) {
            // (Placeholder for complex nested tree if required by UI)
            return $elements;
        };

        return $tree;
    }

    /**
     * Read a file from the workspace.
     */
    public function readFile(Workspace $workspace, string $relativePath): ?string
    {
        $path = '/home/coder/' . $workspace->slug . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        $process = $this->executeDockerFileCommand($workspace, ['cat', $path]);
        
        if ($process->isSuccessful()) {
            return $process->getOutput();
        }
        return null;
    }

    /**
     * Write content to a file in the workspace.
     */
    public function writeFile(Workspace $workspace, string $relativePath, string $content): bool
    {
        $path = '/home/coder/' . $workspace->slug . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        $dir = dirname($path);
        
        // Ensure directory exists
        $this->executeDockerFileCommand($workspace, ['sh', '-c', "mkdir -p '$dir'"]);
        
        $process = $this->executeDockerFileCommand($workspace, ['sh', '-c', "cat > '$path'"], $content);
        
        if ($process->isSuccessful()) {
            Log::info("CodeServerManager: File written", ['workspace' => $workspace->id, 'path' => $relativePath]);
            return true;
        }
        return false;
    }

    /**
     * Delete a file from the workspace.
     */
    public function deleteFile(Workspace $workspace, string $relativePath): bool
    {
        $path = '/home/coder/' . $workspace->slug . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        $process = $this->executeDockerFileCommand($workspace, ['rm', '-rf', $path]);
        return $process->isSuccessful();
    }

    /**
     * Create a new file or directory in the workspace.
     */
    public function createFile(Workspace $workspace, string $relativePath, bool $isDirectory = false): bool
    {
        $path = '/home/coder/' . $workspace->slug . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($isDirectory) {
            $process = $this->executeDockerFileCommand($workspace, ['mkdir', '-p', $path]);
        } else {
            $dir = dirname($path);
            $this->executeDockerFileCommand($workspace, ['sh', '-c', "mkdir -p '$dir'"]);
            $process = $this->executeDockerFileCommand($workspace, ['touch', $path]);
        }
        return $process->isSuccessful();
    }

    /**
     * Rename a file or directory in the workspace.
     */
    public function renameFile(Workspace $workspace, string $oldPath, string $newPath): bool
    {
        $oldP = '/home/coder/' . $workspace->slug . '/' . ltrim(str_replace('\\', '/', $oldPath), '/');
        $newP = '/home/coder/' . $workspace->slug . '/' . ltrim(str_replace('\\', '/', $newPath), '/');
        $process = $this->executeDockerFileCommand($workspace, ['mv', $oldP, $newP]);
        return $process->isSuccessful();
    }

    // ══════════════════════════════════════════════════════════════════════
    // Private Helpers
    // ══════════════════════════════════════════════════════════════════════

    private function ensureWorkspaceDirectory(Workspace $workspace): void
    {
        // No longer creates a physical host directory.
        // Instead, we just seed default files into the Docker volume if they don't exist yet.
        $this->seedDefaultFiles($workspace);
    }

    public function resolveSecurePath(Workspace $workspace, string $relativePath): ?string
    {
        // Deprecated: Kept for signature compatibility but should no longer be used for local file reads.
        // We now use Docker Native Volumes.
        return null;
    }

    public function getSecureFilePath(Workspace $workspace, string $relativePath): ?string
    {
        $path = $this->resolveSecurePath($workspace, $relativePath);
        if ($path && is_file($path)) {
            return $path;
        }
        return null;
    }

    private function resolveQuota(Workspace $workspace): array
    {
        $quota = WorkspaceQuota::resolveFor($workspace->student_id, $workspace->course_id);

        return [
            'memory_mb'       => $quota->memory_mb ?? 2048,
            'cpu_shares'      => $quota->cpu_shares ?? 1024,
            'disk_mb'         => $quota->disk_mb ?? 1024,
            'timeout_minutes' => $quota->timeout_minutes ?? 120,
        ];
    }

    private function seedDefaultFiles(Workspace $workspace): void
    {
        // Check if main file or README already exists to prevent re-seeding
        $existing = $this->readFile($workspace, 'README.md');
        if ($existing !== null) return;

        // If template has a git_url, clone it
        if ($workspace->template_id && $workspace->template && $workspace->template->git_url) {
            $process = $this->executeDockerFileCommand($workspace, ['git', 'clone', $workspace->template->git_url, '.']);
            if ($process->isSuccessful()) {
                $this->executeDockerFileCommand($workspace, ['rm', '-rf', '.git']);
                
                // Inject declarative Nix configuration and bootstrap script even if cloned
                $this->injectVisionConfig($workspace);

                return; // Successfully seeded from template
            }
            Log::error("Workspace template clone failed", ['url' => $workspace->template->git_url, 'error' => $process->getErrorOutput()]);
        }

        // Inject declarative Nix configuration and bootstrap script
        $this->injectVisionConfig($workspace);

        $lang = $workspace->language ?? 'python';

        // If assignment has starter code, use that
        if ($workspace->assignment && $workspace->assignment->starter_code) {
            $ext = $this->fileExtension($lang);
            $this->writeFile($workspace, "main.{$ext}", $workspace->assignment->starter_code);
        } else {
            $this->writeFile($workspace, 'main.' . $this->fileExtension($lang), $this->starterCode($lang));
        }

        $this->writeFile($workspace, 'README.md', "# {$workspace->name}\n\n> Powered by **VisionLab** — Aptech Vision 2026\n\n## Getting Started\n\n1. Write your code in the editor\n2. Use the integrated terminal to run it\n3. Open the AI sidebar for assistance\n");
    }

    private function fileExtension(string $lang): string
    {
        return match ($lang) {
            'python'     => 'py',
            'javascript' => 'js',
            'typescript' => 'ts',
            'php'        => 'php',
            'java'       => 'java',
            'c'          => 'c',
            'cpp', 'c++' => 'cpp',
            'rust'       => 'rs',
            'go'         => 'go',
            'ruby'       => 'rb',
            'bash'       => 'sh',
            default      => 'py',
        };
    }

    private function injectVisionConfig(Workspace $workspace): void
    {
        if ($workspace->template_id && $workspace->template) {
            if (!empty($workspace->template->nix_config) || !empty($workspace->template->bootstrap_script)) {
                $this->createFile($workspace, '.vision', true);
                
                if (!empty($workspace->template->nix_config)) {
                    $this->writeFile($workspace, '.vision/dev.nix', $workspace->template->nix_config);
                    $this->executeDockerFileCommand($workspace, ['chmod', '0666', "/home/coder/{$workspace->slug}/.vision/dev.nix"]);
                }
                
                if (!empty($workspace->template->bootstrap_script)) {
                    $this->writeFile($workspace, '.vision/bootstrap.sh', $workspace->template->bootstrap_script);
                    $this->executeDockerFileCommand($workspace, ['chmod', '0777', "/home/coder/{$workspace->slug}/.vision/bootstrap.sh"]);
                }
            }
        }
    }

    private function starterCode(string $lang): string
    {
        return match ($lang) {
            'python' => "# VisionLab Workspace\n\ndef greet(name: str) -> str:\n    \"\"\"Return a personalised greeting.\"\"\"\n    return f\"Hello, {name}! Welcome to VisionLab.\"\n\nif __name__ == \"__main__\":\n    print(greet(\"World\"))\n",
            'javascript' => "// VisionLab Workspace\n\nconst greet = (name) => {\n    return `Hello, \${name}! Welcome to VisionLab.`;\n};\n\nconsole.log(greet('World'));\n",
            'php' => "<?php\n\nfunction greet(string \$name): string {\n    return \"Hello, {\$name}! Welcome to VisionLab.\";\n}\n\necho greet('World') . PHP_EOL;\n",
            default => "# VisionLab Workspace\nprint('Hello, World!')\n",
        };
    }

    private function dockerCmd(): string
    {
        return PHP_OS_FAMILY === 'Windows' ? 'docker' : '/usr/bin/docker';
    }

    public function isDockerAvailable(): bool
    {
        if (self::$dockerAvailable !== null) {
            return self::$dockerAvailable;
        }

        try {
            $process = new Process([$this->dockerCmd(), 'info']);
            $process->setTimeout(15);
            $process->run();
            self::$dockerAvailable = $process->isSuccessful();
            return self::$dockerAvailable;
        } catch (\Throwable $e) {
            self::$dockerAvailable = false;
            return false;
        }
    }

    private function allocatePort(): int
    {
        // Check DB for used ports first
        $usedPorts = Workspace::whereNotNull('port')
            ->whereIn('status', ['running', 'pending'])
            ->pluck('port')
            ->toArray();

        for ($port = self::PORT_MIN; $port <= self::PORT_MAX; $port++) {
            if (!in_array($port, $usedPorts)) {
                $socket = @fsockopen('localhost', $port, $errno, $errstr, 0.1);
                if ($socket === false) {
                    return $port;
                }
                fclose($socket);
            }
        }

        return rand(self::PORT_MIN, self::PORT_MAX);
    }

    private function removeContainer(string $name): bool
    {
        $stop = new Process([$this->dockerCmd(), 'stop', $name]);
        $stop->setTimeout(15);
        $stop->run();

        $rm = new Process([$this->dockerCmd(), 'rm', '-f', $name]);
        $rm->setTimeout(10);
        $rm->run();

        return $rm->isSuccessful();
    }

    public function installExtension(Workspace $workspace, string $identifierOrPath, ?string $expectedChecksum = null): bool
    {
        $containerName = "vl_ws_{$workspace->id}";
        
        // If it is a local .vsix artifact, enforce SHA256 integrity
        if (str_ends_with($identifierOrPath, '.vsix')) {
            // Identifier could be 'extensions/test.vsix' or just 'test.vsix'
            $fileName = basename($identifierOrPath);
            $vsixPath = storage_path('app/extensions/' . $fileName);
            
            if (!file_exists($vsixPath)) {
                \Illuminate\Support\Facades\Log::error("Extension artifact not found", ['target' => $identifierOrPath]);
                return false;
            }

            if ($expectedChecksum) {
                $actualChecksum = hash_file('sha256', $vsixPath);
                if ($actualChecksum !== $expectedChecksum) {
                    event(new \App\Events\ExtensionIntegrityFailed($workspace, $identifierOrPath, $expectedChecksum, $actualChecksum));
                    throw new \App\Exceptions\ExtensionIntegrityException(
                        "SHA256 checksum mismatch for extension {$identifierOrPath}. Expected {$expectedChecksum}, got {$actualChecksum}"
                    );
                }
            }
        }

        if (str_ends_with($identifierOrPath, '.vsix')) {
            $extDirName = "built-in-" . pathinfo($fileName, PATHINFO_FILENAME);
            $cmdStr = "mkdir -p /tmp/$extDirName && unzip -q /var/opt/extensions/$fileName -d /tmp/$extDirName && rm -rf /usr/lib/code-server/lib/vscode/extensions/$extDirName && mv /tmp/$extDirName/extension /usr/lib/code-server/lib/vscode/extensions/$extDirName && rm -rf /tmp/$extDirName";
            
            $process = new Process([$this->dockerCmd(), 'exec', '-u', '1000', $containerName, 'sh', '-c', $cmdStr]);
        } else {
            $process = new Process([$this->dockerCmd(), 'exec', '-u', '1000', $containerName, 'code-server', '--install-extension', $identifierOrPath]);
        }

        $process->setTimeout(120);
        $process->run();
        
        if (!$process->isSuccessful()) {
            \Illuminate\Support\Facades\Log::error("Failed to install extension $identifierOrPath in $containerName: " . $process->getErrorOutput());
        }
        return $process->isSuccessful();
    }

    public function uninstallExtension(Workspace $workspace, string $identifier): bool
    {
        $containerName = "vl_ws_{$workspace->id}";
        $process = new Process([$this->dockerCmd(), 'exec', '-u', '1000', $containerName, 'code-server', '--uninstall-extension', $identifier]);
        $process->run();
        
        if (!$process->isSuccessful()) {
            \Illuminate\Support\Facades\Log::error("Failed to uninstall extension $identifier in $containerName: " . $process->getErrorOutput());
        }
        return $process->isSuccessful();
    }

    private function deleteDirectory(string $dir): void
    {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function setupWorkspaceExtensions(string $containerName, Workspace $workspace): void
    {
        // 1. Setup VisionLab Agent Config
        $token = $workspace->owner?->createToken('workspace')->plainTextToken ?? '';

        $config = [
            "models" => [
                [
                    "title" => "VisionLab Sonnet 3.5",
                    "model" => "claude-3-5-sonnet-20241022",
                    "provider" => "openai",
                    "apiBase" => "http://host.docker.internal:8000/api/ai/v1",
                    "apiKey" => $token
                ],
                [
                    "title" => "VisionLab Opus 3",
                    "model" => "claude-3-opus-20240229",
                    "provider" => "openai",
                    "apiBase" => "http://host.docker.internal:8000/api/ai/v1",
                    "apiKey" => $token
                ],
                [
                    "title" => "VisionLab GPT-4o",
                    "model" => "gpt-4o",
                    "provider" => "openai",
                    "apiBase" => "http://host.docker.internal:8000/api/ai/v1",
                    "apiKey" => $token
                ]
            ],
            "customCommands" => [
                [
                    "name" => "agent",
                    "prompt" => "You are an autonomous agent. Propose a patch for the following request using your tools.",
                    "description" => "Propose a patch for a task"
                ],
                [
                    "name" => "plan",
                    "prompt" => "Create a step-by-step implementation plan for the following feature. Do not propose any patches yet.",
                    "description" => "Plan a feature implementation"
                ],
                [
                    "name" => "ask",
                    "prompt" => "Answer the following question or explain the code. Do not propose any patches.",
                    "description" => "Ask a question about the code"
                ]
            ],
            "allowAnonymousTelemetry" => false
        ];

        $json = json_encode($config, JSON_UNESCAPED_SLASHES);
        $jsonForBash = str_replace("'", "'\\''", $json);
        $script = "mkdir -p /home/coder/.continue && echo '{$jsonForBash}' > /home/coder/.continue/config.json";

        $process = new Process([$this->dockerCmd(), 'exec', $containerName, 'bash', '-c', $script]);
        $process->run();

        // 2. Auto-populate workspace_extensions pivot if empty (first boot)
        $existingPivots = \App\Models\WorkspaceExtension::where('workspace_id', $workspace->id)->count();

        if ($existingPivots === 0) {
            // First boot: assign all active global extensions to this workspace
            $globalExtensions = \App\Models\Extension::where('is_global', true)
                ->where('is_active', true)
                ->get();

            foreach ($globalExtensions as $ext) {
                \App\Models\WorkspaceExtension::create([
                    'workspace_id'  => $workspace->id,
                    'extension_id'  => $ext->id,
                    'is_enabled'    => true,
                    'policy_source' => 'global_auto',
                    'sync_status'   => 'pending',
                ]);
            }

            Log::info("Auto-populated {$globalExtensions->count()} global extensions for workspace {$workspace->id}");
        }

        // 3. Install all enabled extensions from the pivot table
        $enabledExtensions = \App\Models\WorkspaceExtension::with('extension')
            ->where('workspace_id', $workspace->id)
            ->where('is_enabled', true)
            ->get();

        foreach ($enabledExtensions as $wsExt) {
            $ext = $wsExt->extension;
            if (!$ext || !$ext->is_active) continue;
            
            $identifier = $ext->package_identifier;
            $target = $ext->artifact_path ?? $identifier;
            
            try {
                $this->installExtension($workspace, $target, $ext->checksum);
                $wsExt->update(['sync_status' => 'synced']);
            } catch (\Throwable $e) {
                $wsExt->update(['sync_status' => 'failed']);
                Log::warning("Failed to install extension {$identifier}: " . $e->getMessage());
            }
        }

    }



    /**
     * Run a terminal command inside the workspace container (or local fallback).
     */
    public function runTerminalCommand(Workspace $workspace, string $command): string
    {
        if (!$this->isDockerAvailable()) {
            $workspacePath = $this->workspacePath($workspace);
            if (!is_dir($workspacePath)) {
                mkdir($workspacePath, 0755, true);
            }
            try {
                $process = Process::fromShellCommandline($command);
                $process->setWorkingDirectory($workspacePath);
                $process->setTimeout(15);
                $process->run();
                return $process->getOutput() ?: $process->getErrorOutput() ?: 'Command completed with no output.';
            } catch (\Throwable $e) {
                return 'Error executing local fallback: ' . $e->getMessage();
            }
        }

        $containerName = 'vl_ws_' . $workspace->id;
        $process = new Process([
            $this->dockerCmd(), 'exec', '-w', '/home/coder/' . $workspace->slug, $containerName,
            'timeout', '15', 'bash', '-c', $command
        ]);
        $process->setTimeout(20);
        $process->run();

        return $process->getOutput() ?: $process->getErrorOutput() ?: 'Command completed with no output.';
    }

    private function devFallback(Workspace $workspace): array
    {
        $devPort = (int) env('VSCODE_SERVER_PORT', 8099);
        $devUrl  = env('VSCODE_SERVER_URL', "http://127.0.0.1:{$devPort}/");

        return [
            'url'          => $devUrl,
            'port'         => $devPort,
            'token'        => 'dev-mode',
            'container_id' => null,
        ];
    }
}
