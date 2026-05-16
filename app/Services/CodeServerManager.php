<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * CodeServerManager — Manages code-server Docker containers per workspace.
 *
 * In development (Docker unavailable), returns a fallback URL pointing to
 * a locally running OpenVSCode Server or shows a placeholder UI.
 *
 * In production, spawns a Docker container per workspace using codercom/code-server.
 */
class CodeServerManager
{
    /** Port range for dynamically allocated code-server instances */
    private const PORT_MIN = 9000;
    private const PORT_MAX = 9999;

    /** Docker image for code-server */
    private const DOCKER_IMAGE = 'codercom/code-server:4.19.1';

    /**
     * Start a workspace container.
     *
     * @param Room $room  The workspace/room to start
     * @return array{url: string, port: int, token: string, container_id: string|null}
     */
    public function startWorkspace(Room $room): array
    {
        $workspacePath = $this->ensureWorkspaceDirectory($room);

        // Development mode — Docker not available
        if (!$this->isDockerAvailable()) {
            Log::info("CodeServerManager: Docker unavailable, using dev fallback for room {$room->slug}");
            return $this->devFallback($room);
        }

        // Check if container already running
        $existingStatus = $this->getStatus($room);
        if ($existingStatus['running']) {
            return [
                'url'          => $existingStatus['url'],
                'port'         => $existingStatus['port'],
                'token'        => $existingStatus['token'],
                'container_id' => $existingStatus['container_id'],
            ];
        }

        $port  = $this->allocatePort();
        $token = Str::random(32);
        $name  = 'VisionLab_ws_' . $room->slug;

        // Check database configuration for marketplace access
        $allowMarketplace = \App\Models\AiSetting::getValue('allow_marketplace', 'true') === 'true';

        // Remove any stale container with the same name
        $this->removeContainer($name);

        // Fetch Resource Quotas
        $quota = \App\Models\WorkspaceQuota::resolveForRoom($room);

        $cmd = [
            'docker', 'run', '-d',
            '--name', $name,
            '-v', "{$workspacePath}:/home/coder/project",
            '-v', storage_path('extensions') . ":/usr/lib/code-server/extensions:ro", // Immutable mount
            '-p', "{$port}:8080",
            '-e', "VisionLab_API_TOKEN=" . ($room->owner?->createToken('workspace')->plainTextToken ?? ''),
            '-e', "VisionLab_ROOM_SLUG={$room->slug}",
            '-e', "VisionLab_USER_ID=" . (auth()->id() ?? '0'),
            '-e', "VisionLab_USER_NAME=" . (auth()->user()?->name ?? 'Guest'),
            '-e', "VisionLab_API_URL=" . url('/api'),
            '--restart', 'unless-stopped',
            '--memory', $quota->memory_limit,
            '--cpus', $quota->cpu_limit,
            self::DOCKER_IMAGE,
            '--auth', 'none',
            '--bind-addr', '0.0.0.0:8080',
            '--extensions-dir', '/usr/lib/code-server/extensions',
        ];

        // Disable marketplace completely if teacher/admin blocked it
        if (!$allowMarketplace) {
            $cmd[] = '--disable-marketplace';
        }

        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("CodeServerManager: Failed to start container", [
                'room'   => $room->slug,
                'error'  => $process->getErrorOutput(),
                'output' => $process->getOutput(),
            ]);

            return $this->devFallback($room);
        }

        $containerId = trim($process->getOutput());

        Log::info("CodeServerManager: Started container", [
            'room'         => $room->slug,
            'container_id' => substr($containerId, 0, 12),
            'port'         => $port,
        ]);

        // Inject Continue config
        $this->setupContinueExtension($name, $room);

        return [
            'url'          => "http://localhost:{$port}/?folder=/home/coder/project",
            'port'         => $port,
            'token'        => $token,
            'container_id' => $containerId,
        ];
    }

    /**
     * Stop and remove a workspace container.
     */
    public function stopWorkspace(Room $room): bool
    {
        $name = 'VisionLab_ws_' . $room->slug;

        if (!$this->isDockerAvailable()) {
            return true;
        }

        return $this->removeContainer($name);
    }

    /**
     * Get the status of a workspace container.
     *
     * @return array{running: bool, url: string|null, port: int|null, token: string|null, container_id: string|null}
     */
    public function getStatus(Room $room): array
    {
        $name = 'VisionLab_ws_' . $room->slug;

        if (!$this->isDockerAvailable()) {
            return ['running' => false, 'url' => null, 'port' => null, 'token' => null, 'container_id' => null];
        }

        $process = new Process(['docker', 'inspect', '--format', '{{.State.Running}}:{{.Id}}', $name]);
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
        $portProcess = new Process(['docker', 'port', $name, '8080']);
        $portProcess->setTimeout(10);
        $portProcess->run();
        $portOutput = trim($portProcess->getOutput());
        preg_match('/:(\d+)$/', $portOutput, $portMatch);
        $port = isset($portMatch[1]) ? (int)$portMatch[1] : null;

        return [
            'running'      => true,
            'url'          => $port ? "http://localhost:{$port}/?folder=/home/coder/project" : null,
            'port'         => $port,
            'token'        => null, // Token is set at creation, cannot retrieve from running container
            'container_id' => $id,
        ];
    }

    /**
     * List files in a workspace directory (recursive tree).
     *
     * @return array<int, array{name: string, path: string, type: string, children?: array}>
     */
    public function listFiles(Room $room, string $relativePath = ''): array
    {
        $basePath = $this->workspacePath($room);
        $fullPath = $basePath . ($relativePath ? DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\') : '');

        // Security: prevent path traversal
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
            // Skip hidden files/directories
            if (str_starts_with($item, '.') && $item !== '.VisionLab_memory.md') continue;

            $itemPath = $realFull . DIRECTORY_SEPARATOR . $item;
            $relPath  = ltrim(str_replace($realBase, '', $itemPath), DIRECTORY_SEPARATOR);

            $entry = [
                'name' => $item,
                'path' => str_replace('\\', '/', $relPath),
                'type' => is_dir($itemPath) ? 'directory' : 'file',
            ];

            if (is_dir($itemPath)) {
                $entry['children'] = $this->listFiles($room, $relPath);
            }

            $tree[] = $entry;
        }

        // Sort: directories first, then alphabetical
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
    public function readFile(Room $room, string $relativePath): ?string
    {
        $fullPath = $this->resolveSecurePath($room, $relativePath);
        if ($fullPath === null || !is_file($fullPath)) {
            return null;
        }

        return file_get_contents($fullPath);
    }

    /**
     * Write content to a file in the workspace.
     */
    public function writeFile(Room $room, string $relativePath, string $content): bool
    {
        $basePath = $this->workspacePath($room);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        // Security: ensure the resolved path stays within workspace
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
            'room' => $room->slug,
            'path' => $relativePath,
            'size' => strlen($content),
        ]);

        return true;
    }

    /**
     * Delete a file from the workspace.
     */
    public function deleteFile(Room $room, string $relativePath): bool
    {
        $fullPath = $this->resolveSecurePath($room, $relativePath);
        if ($fullPath === null) return false;

        if (is_dir($fullPath)) {
            // Recursively delete directory
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
    public function createFile(Room $room, string $relativePath, bool $isDirectory = false): bool
    {
        $basePath = $this->workspacePath($room);
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
    public function renameFile(Room $room, string $oldPath, string $newPath): bool
    {
        $oldFull = $this->resolveSecurePath($room, $oldPath);
        if ($oldFull === null) return false;

        $basePath = $this->workspacePath($room);
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

    // ── Private helpers ──────────────────────────────────────────────

    private function ensureWorkspaceDirectory(Room $room): string
    {
        $path = $this->workspacePath($room);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);

            // Create default files for new workspaces
            $this->seedDefaultFiles($path, $room);
        }

        return $path;
    }

    private function workspacePath(Room $room): string
    {
        return storage_path('workspaces' . DIRECTORY_SEPARATOR . $room->slug);
    }

    private function resolveSecurePath(Room $room, string $relativePath): ?string
    {
        $basePath = $this->workspacePath($room);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        $realBase = realpath($basePath);
        $realFull = realpath($fullPath);

        if ($realBase === false || $realFull === false || !str_starts_with($realFull, $realBase)) {
            Log::warning("CodeServerManager: Path traversal blocked", ['path' => $relativePath]);
            return null;
        }

        return $realFull;
    }

    private function seedDefaultFiles(string $path, Room $room): void
    {
        $lang = $room->language ?? 'python';

        $files = [
            'README.md' => "# {$room->name}\n\n> Powered by **VisionLab** — Aptech Vision 2026\n\n## Getting Started\n\n1. Write your code in the editor\n2. Use the integrated terminal to run it\n3. Open the AI sidebar for assistance\n",
            'main.' . $this->fileExtension($lang) => $this->starterCode($lang),
        ];

        foreach ($files as $name => $content) {
            file_put_contents($path . DIRECTORY_SEPARATOR . $name, $content);
        }
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
            default      => 'py',
        };
    }

    private function starterCode(string $lang): string
    {
        return match ($lang) {
            'python' => "# VisionLabWorkspace\n\ndef greet(name: str) -> str:\n    \"\"\"Return a personalised greeting.\"\"\"\n    return f\"Hello, {name}! Welcome to VisionLab.\"\n\nif __name__ == \"__main__\":\n    print(greet(\"World\"))\n",
            'javascript' => "// VisionLabWorkspace\n\nconst greet = (name) => {\n    return `Hello, \${name}! Welcome to VisionLab.`;\n};\n\nconsole.log(greet('World'));\n",
            'php' => "<?php\n\nfunction greet(string \$name): string {\n    return \"Hello, {\$name}! Welcome to VisionLab.\";\n}\n\necho greet('World') . PHP_EOL;\n",
            default => "# VisionLabWorkspace\nprint('Hello, World!')\n",
        };
    }

    private function isDockerAvailable(): bool
    {
        $process = new Process(['docker', 'info']);
        $process->setTimeout(5);
        $process->run();

        return $process->isSuccessful();
    }

    private function allocatePort(): int
    {
        // Find an available port in our range
        for ($port = self::PORT_MIN; $port <= self::PORT_MAX; $port++) {
            $socket = @fsockopen('localhost', $port, $errno, $errstr, 0.1);
            if ($socket === false) {
                return $port; // Port is available
            }
            fclose($socket);
        }

        // Fallback: random port in range
        return rand(self::PORT_MIN, self::PORT_MAX);
    }

    private function removeContainer(string $name): bool
    {
        $stop = new Process(['docker', 'stop', $name]);
        $stop->setTimeout(15);
        $stop->run();

        $rm = new Process(['docker', 'rm', '-f', $name]);
        $rm->setTimeout(10);
        $rm->run();

        return $rm->isSuccessful();
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

    /**
     * Set up the Continue AI extension inside the container.
     */
    private function setupContinueExtension(string $containerName, Room $room): void
    {
        $token = $room->owner?->createToken('workspace')->plainTextToken ?? '';
        $hostBaseUrl = env('AI_PROXY_URL', 'http://host.docker.internal:8000/api/ai/v1');

        // Fetch active chat/agent models
        $activeModels = \App\Models\AiModel::getActiveChatModels();
        $modelsConfig = [];

        foreach ($activeModels as $m) {
            $modelsConfig[] = [
                'title' => $m->display_name,
                'model' => $m->model_id,
                'provider' => 'openai', // We masquerade all as openai to funnel through our proxy
                'apiBase' => $hostBaseUrl,
                'apiKey' => $token,
                'contextLength' => $m->context_length,
            ];
        }

        // Fetch autocomplete model (Gemini 2.5 Flash by default)
        $autocompleteModel = \App\Models\AiModel::getAutocompleteModel();
        $tabAutocompleteModel = null;
        if ($autocompleteModel) {
            $tabAutocompleteModel = [
                'title' => $autocompleteModel->display_name,
                'model' => $autocompleteModel->model_id,
                'provider' => 'openai',
                'apiBase' => $hostBaseUrl,
                'apiKey' => $token,
            ];
        }

        $config = [
            "models" => $modelsConfig,
            "tabAutocompleteModel" => $tabAutocompleteModel,
            "tabAutocompleteOptions" => [
                "useCopyBuffer" => false,
                "maxPromptTokens" => 1024,
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
            "allowAnonymousTelemetry" => \App\Models\AiSetting::getValue('telemetry_enabled', 'false') === 'true',
            "disableIndexing" => \App\Models\AiSetting::getValue('indexing_enabled', 'false') !== 'true',
        ];

        $json = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        
        $vscodeSettings = [
            "workbench.colorTheme" => "Default Dark Modern",
            "window.title" => "VisionLab Workspace",
            "workbench.startupEditor" => "none",
            "workbench.welcomePage.walkthroughs.openOnInstall" => false,
            "editor.fontFamily" => "'JetBrains Mono', 'Fira Code', 'Inter', monospace",
            "editor.fontSize" => 14,
            "editor.fontLigatures" => true,
            "editor.smoothScrolling" => true,
            "editor.cursorBlinking" => "smooth",
            "editor.cursorSmoothCaretAnimation" => "on",
            "editor.formatOnSave" => true,
            "workbench.colorCustomizations" => [
                "[Default Dark Modern]" => [
                    "titleBar.activeBackground" => "#151515",
                    "titleBar.inactiveBackground" => "#151515",
                    "titleBar.activeForeground" => "#f1f5f9",
                    "editor.background" => "#151515",
                    "sideBar.background" => "#1a1a1a",
                    "activityBar.background" => "#151515",
                    "statusBar.background" => "#1a1a1a",
                    "tab.activeBackground" => "#222222",
                    "tab.inactiveBackground" => "#151515",
                    "tab.border" => "#333333",
                    "panel.background" => "#151515",
                    "focusBorder" => "#F05000",
                    "button.background" => "#F05000",
                    "button.hoverBackground" => "#d04000",
                    "textLink.foreground" => "#F05000",
                    "textLink.activeForeground" => "#ff732e"
                ]
            ]
        ];
        $settingsJson = json_encode($vscodeSettings, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $script = "mkdir -p /home/coder/.continue /home/coder/.local/share/code-server/User && " .
                  "cat << 'EOF' > /home/coder/.continue/config.json\n" . $json . "\nEOF\n" .
                  "cat << 'EOF' > /home/coder/.local/share/code-server/User/settings.json\n" . $settingsJson . "\nEOF";
        
        $process = new Process(['docker', 'exec', $containerName, 'bash', '-c', $script]);
        $process->run();

        // ── DEEP REBRANDING (Run as root) ──
        $patchScript = <<< 'EOF'
node -e "
const fs = require('fs');
const pPath = '/usr/lib/code-server/lib/vscode/product.json';
if (fs.existsSync(pPath)) {
    let p = JSON.parse(fs.readFileSync(pPath, 'utf8'));
    p.nameShort = 'VisionLab';
    p.nameLong = 'VisionLab IDE';
    p.applicationName = 'visionlab';
    p.dataFolderName = '.visionlab';
    fs.writeFileSync(pPath, JSON.stringify(p, null, 2));
}

const wPath = '/usr/lib/code-server/lib/vscode/out/vs/code/browser/workbench/workbench.html';
if (fs.existsSync(wPath)) {
    let w = fs.readFileSync(wPath, 'utf8');
    if (!w.includes('VisionLab Deep Rebranding')) {
        const css = \`<style>
        /* VisionLab Deep Rebranding CSS */
        .monaco-workbench .part.titlebar { background: #0a0a0a !important; border-bottom: 1px solid #21262d !important; }
        .titlebar-left .window-appicon { display: none !important; }
        .titlebar-center .window-title::before { content: 'VisionLab Workspace' !important; color: #f1f5f9 !important; font-weight: 800 !important; font-size: 13px !important; letter-spacing: 0.5px !important; }
        .monaco-workbench .part.editor { border-radius: 12px !important; margin: 8px !important; overflow: hidden !important; border: 1px solid #21262d !important; box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important; }
        .monaco-workbench .part.activitybar { background: transparent !important; }
        .monaco-workbench .part.sidebar { border-radius: 12px !important; margin: 8px 0 8px 8px !important; border: 1px solid #21262d !important; overflow: hidden !important; }
        .monaco-workbench .part.panel { border-radius: 12px !important; margin: 0 8px 8px 8px !important; border: 1px solid #21262d !important; overflow: hidden !important; }
        .monaco-workbench .part.statusbar { border-top: 1px solid #21262d !important; background: #0d1117 !important; }
        .monaco-workbench .part > .content { border-radius: inherit !important; }
        </style>\`;
        w = w.replace('</head>', css + '</head>');
        fs.writeFileSync(wPath, w);
    }
}
"
EOF;
        $patchProcess = new Process(['docker', 'exec', '-u', 'root', $containerName, 'bash', '-c', $patchScript]);
        $patchProcess->run();
    }

    /**
     * Dev fallback when Docker is not available.
     */
    private function devFallback(Room $room): array
    {
        // Try OpenVSCode Server on standard ports
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
