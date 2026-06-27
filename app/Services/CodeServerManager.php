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
        $workspacePath = $this->ensureWorkspaceDirectory($workspace);

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
            ? "http://localhost:{$port}/?folder=/home/coder/project" 
            : "/ide/{$port}/?folder=/home/coder/project";

        $authMode = 'none'; // Permanently disabled password auth as requested

        if (!is_dir($workspacePath)) {
            mkdir($workspacePath, 0777, true);
            exec('chmod -R 0777 ' . escapeshellarg($workspacePath));
            
            // Inject dev.nix if a template is assigned
            if ($workspace->template_id && $workspace->template) {
                if (!empty($workspace->template->nix_config)) {
                    file_put_contents($workspacePath . '/dev.nix', $workspace->template->nix_config);
                    chmod($workspacePath . '/dev.nix', 0666);
                }
            }
        }

        $extensionsPath = storage_path('app/extensions');
        if (!is_dir($extensionsPath)) {
            mkdir($extensionsPath, 0755, true);
        }

        $disableMarketplace = false;
        if ($workspace->course_id && $workspace->course) {
            $disableMarketplace = !$workspace->course->allow_marketplace;
        }

        $cmd = [
            $this->dockerCmd(), 'run', '-d', '--init',
            '--name', $name,
            '--cap-drop', 'ALL',
            '--tmpfs', '/tmp',
            '--tmpfs', '/run',
            '--network', 'visionlab-workspace-net',
            '-v', "{$workspacePath}:/home/coder/project",
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
            '--memory', ($quota['memory_mb'] ?? 512) . 'm',
            '--cpu-shares', (string) ($quota['cpu_shares'] ?? 1024),
            $image,
            '--auth', $authMode,
            '--bind-addr', '0.0.0.0:8080',
            '--disable-telemetry',
            '--trusted-origins', 'https://visionlab.ayan24.me',
        ]);

        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("CodeServerManager: Failed to start container", [
                'workspace' => $workspace->id,
                'error'     => $process->getErrorOutput(),
                'output'    => $process->getOutput(),
            ]);

            return $this->devFallback($workspace);
        }

        $containerId = trim($process->getOutput());

        // Update workspace record
        $workspace->update([
            'container_id'    => $containerId,
            'port'            => $port,
            'token'           => $token,
            'storage_path'    => $workspacePath,
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
            ? "http://localhost:{$port}/?folder=/home/coder/project" 
            : "/ide/{$port}/?folder=/home/coder/project";

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
        if (!$workspace->port || !$this->isDockerAvailable()) {
            return false;
        }

        try {
            // Fast ping to the Docker bound port.
            // If visionlab-ide hasn't bound the internal 8080 port yet, this will fail or timeout.
            // Using 'localhost' instead of '127.0.0.1' for better Docker Desktop Windows compatibility
            $response = \Illuminate\Support\Facades\Http::timeout(2)
                ->get("http://localhost:{$workspace->port}/healthz");

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
     * List files in a workspace directory (recursive tree).
     */
    public function listFiles(Workspace $workspace, string $relativePath = ''): array
    {
        $basePath = $this->workspacePath($workspace);
        $fullPath = $basePath . ($relativePath ? DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\') : '');

        $realBase = realpath($basePath);
        $realFull = realpath($fullPath);

        if ($realBase === false || $realFull === false || !str_starts_with($realFull, $realBase)) {
            return [];
        }

        if (!is_dir($realFull)) {
            return [];
        }

        $tree = [];
        $items = scandir($realFull);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            if (str_starts_with($item, '.') && $item !== '.visioncode_memory.md') continue;

            $itemPath = $realFull . DIRECTORY_SEPARATOR . $item;
            $relPath  = ltrim(str_replace($realBase, '', $itemPath), DIRECTORY_SEPARATOR);

            $entry = [
                'name' => $item,
                'path' => str_replace('\\', '/', $relPath),
                'type' => is_dir($itemPath) ? 'directory' : 'file',
            ];

            if (is_dir($itemPath)) {
                $entry['children'] = $this->listFiles($workspace, $relPath);
            }

            $tree[] = $entry;
        }

        usort($tree, function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'directory' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $tree;
    }

    /**
     * Read a file from the workspace.
     */
    public function readFile(Workspace $workspace, string $relativePath): ?string
    {
        $fullPath = $this->resolveSecurePath($workspace, $relativePath);
        if ($fullPath === null || !is_file($fullPath)) {
            return null;
        }
        return file_get_contents($fullPath);
    }

    /**
     * Write content to a file in the workspace.
     */
    public function writeFile(Workspace $workspace, string $relativePath, string $content): bool
    {
        $basePath = $this->workspacePath($workspace);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        $realBase = realpath($basePath);
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $realDir = realpath($dir);
        if ($realDir === false || !str_starts_with($realDir, $realBase)) {
            Log::warning("CodeServerManager: Path traversal attempt", ['path' => $relativePath]);
            return false;
        }

        file_put_contents($fullPath, $content);

        Log::info("CodeServerManager: File written", [
            'workspace' => $workspace->id,
            'path'      => $relativePath,
            'size'      => strlen($content),
        ]);

        return true;
    }

    /**
     * Delete a file from the workspace.
     */
    public function deleteFile(Workspace $workspace, string $relativePath): bool
    {
        $fullPath = $this->resolveSecurePath($workspace, $relativePath);
        if ($fullPath === null) return false;

        if (is_dir($fullPath)) {
            $this->deleteDirectory($fullPath);
            return true;
        }

        if (is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Create a new file or directory in the workspace.
     */
    public function createFile(Workspace $workspace, string $relativePath, bool $isDirectory = false): bool
    {
        $basePath = $this->workspacePath($workspace);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        $realBase = realpath($basePath);
        $parentDir = dirname($fullPath);

        if (!is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        $realParent = realpath($parentDir);
        if ($realParent === false || !str_starts_with($realParent, $realBase)) {
            return false;
        }

        if (file_exists($fullPath)) return false;

        if ($isDirectory) {
            return mkdir($fullPath, 0755, true);
        }

        return file_put_contents($fullPath, '') !== false;
    }

    /**
     * Rename a file or directory in the workspace.
     */
    public function renameFile(Workspace $workspace, string $oldPath, string $newPath): bool
    {
        $oldFull = $this->resolveSecurePath($workspace, $oldPath);
        if ($oldFull === null) return false;

        $basePath = $this->workspacePath($workspace);
        $newFull  = $basePath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $newPath), DIRECTORY_SEPARATOR);

        $realBase = realpath($basePath);
        $newDir   = dirname($newFull);

        if (!is_dir($newDir)) {
            mkdir($newDir, 0755, true);
        }

        $realNewDir = realpath($newDir);
        if ($realNewDir === false || !str_starts_with($realNewDir, $realBase)) {
            return false;
        }

        return rename($oldFull, $newFull);
    }

    // ══════════════════════════════════════════════════════════════════════
    // Private Helpers
    // ══════════════════════════════════════════════════════════════════════

    private function ensureWorkspaceDirectory(Workspace $workspace): string
    {
        $path = $this->workspacePath($workspace);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
            $this->seedDefaultFiles($path, $workspace);
        }

        return $path;
    }

    private function workspacePath(Workspace $workspace): string
    {
        return storage_path('workspaces' . DIRECTORY_SEPARATOR . 'ws-' . $workspace->id);
    }

    public function resolveSecurePath(Workspace $workspace, string $relativePath): ?string
    {
        $basePath = $this->workspacePath($workspace);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        $realBase = realpath($basePath);
        $realFull = realpath($fullPath);

        // If file doesn't exist yet, realpath returns false. We can't strictly use realpath for new files.
        // For new files, we resolve the directory instead.
        if ($realFull === false) {
            $dir = dirname($fullPath);
            $realDir = realpath($dir);
            if ($realBase === false || $realDir === false || !str_starts_with($realDir, $realBase)) {
                Log::warning("CodeServerManager: Path traversal blocked (new file)", ['path' => $relativePath]);
                return null;
            }
            // Normalize the full path
            return $realDir . DIRECTORY_SEPARATOR . basename($fullPath);
        }

        if ($realBase === false || !str_starts_with($realFull, $realBase)) {
            Log::warning("CodeServerManager: Path traversal blocked", ['path' => $relativePath]);
            return null;
        }

        return $realFull;
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
            'memory_mb'       => $quota->memory_mb ?? 512,
            'cpu_shares'      => $quota->cpu_shares ?? 1024,
            'disk_mb'         => $quota->disk_mb ?? 1024,
            'timeout_minutes' => $quota->timeout_minutes ?? 120,
        ];
    }

    private function seedDefaultFiles(string $path, Workspace $workspace): void
    {
        // If template has a git_url, clone it
        if ($workspace->template_id && $workspace->template && $workspace->template->git_url) {
            $process = new Process(['git', 'clone', $workspace->template->git_url, '.']);
            $process->setWorkingDirectory($path);
            $process->run();
            
            if ($process->isSuccessful()) {
                $this->deleteDirectory($path . DIRECTORY_SEPARATOR . '.git');
                return; // Successfully seeded from template
            }
            Log::error("Workspace template clone failed", ['url' => $workspace->template->git_url, 'error' => $process->getErrorOutput()]);
        }

        $lang = $workspace->language ?? 'python';

        // If assignment has starter code, use that
        if ($workspace->assignment && $workspace->assignment->starter_code) {
            $ext = $this->fileExtension($lang);
            file_put_contents($path . DIRECTORY_SEPARATOR . "main.{$ext}", $workspace->assignment->starter_code);
        } else {
            file_put_contents(
                $path . DIRECTORY_SEPARATOR . 'main.' . $this->fileExtension($lang),
                $this->starterCode($lang)
            );
        }

        file_put_contents(
            $path . DIRECTORY_SEPARATOR . 'README.md',
            "# {$workspace->name}\n\n> Powered by **VisionLab** — Aptech Vision 2026\n\n## Getting Started\n\n1. Write your code in the editor\n2. Use the integrated terminal to run it\n3. Open the AI sidebar for assistance\n"
        );
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

            $identifierOrPath = "/var/opt/extensions/{$fileName}";
        }

        $process = new Process([$this->dockerCmd(), 'exec', '-u', '1000', $containerName, 'code-server', '--install-extension', $identifierOrPath]);
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

        // 2. Install enabled extensions dynamically from WorkspaceExtension pivot
        $enabledExtensions = \App\Models\WorkspaceExtension::with('extension')
            ->where('workspace_id', $workspace->id)
            ->where('is_enabled', true)
            ->get();

        foreach ($enabledExtensions as $wsExt) {
            $ext = $wsExt->extension;
            if (!$ext || !$ext->is_active) continue;
            
            $identifier = $ext->package_identifier;
            $target = $ext->is_builtin ? $identifier : ($ext->artifact_path ?? $identifier);
            
            $this->installExtension($workspace, $target, $ext->checksum);
            $wsExt->update(['sync_status' => 'synced']);
        }

        // 3. Always install global builtin extensions (e.g. VisionLab Agent)
        $globalExtensions = \App\Models\Extension::where('is_global', true)
            ->where('is_active', true)
            ->get();

        foreach ($globalExtensions as $ext) {
            $identifier = $ext->package_identifier;
            $target = $ext->artifact_path ?? $identifier; // Prioritize local .vsix if available
            $this->installExtension($workspace, $target, $ext->checksum);
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
            $this->dockerCmd(), 'exec', '-w', '/home/coder/project', $containerName,
            'timeout', '15', 'bash', '-c', $command
        ]);
        $process->setTimeout(20);
        $process->run();

        return $process->getOutput() ?: $process->getErrorOutput() ?: 'Command completed with no output.';
    }

    private function devFallback(Workspace $workspace): array
    {
        $devPort = (int) env('VSCODE_SERVER_PORT', 8099);
        $devUrl  = env('VSCODE_SERVER_URL', "http://localhost:{$devPort}/");

        return [
            'url'          => $devUrl,
            'port'         => $devPort,
            'token'        => 'dev-mode',
            'container_id' => null,
        ];
    }
}
