<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\CodeServerManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    private CodeServerManager $codeServerManager;

    public function __construct(CodeServerManager $codeServerManager)
    {
        $this->codeServerManager = $codeServerManager;
    }
    private function defaultFiles(): array
    {
        $name = Auth::user()->name;
        return [
            [
                'id'       => 'main-py',
                'name'     => 'main.py',
                'type'     => 'file',
                'language' => 'python',
                'content'  => "# VisionLab— Workspace\n# Welcome, {$name}!\n\ndef greet(name: str) -> str:\n    \"\"\"Return a personalised greeting.\"\"\"\n    return f\"Hello, {name}! Welcome to VisionLab.\"\n\nif __name__ == \"__main__\":\n    message = greet(\"World\")\n    print(message)\n",
            ],
            [
                'id'       => 'utils-py',
                'name'     => 'utils.py',
                'type'     => 'file',
                'language' => 'python',
                'content'  => "# Utility helpers\n\ndef add(a: int, b: int) -> int:\n    return a + b\n\ndef subtract(a: int, b: int) -> int:\n    return a - b\n\ndef multiply(a: int, b: int) -> int:\n    return a * b\n",
            ],
            [
                'id'       => 'index-js',
                'name'     => 'index.js',
                'type'     => 'file',
                'language' => 'javascript',
                'content'  => "// VisionLab— JavaScript Workspace\n\nconst greet = (name) => {\n    return `Hello, \${name}! Welcome to VisionLab.`;\n};\n\nconsole.log(greet('World'));\n",
            ],
            [
                'id'       => 'hello-php',
                'name'     => 'hello.php',
                'type'     => 'file',
                'language' => 'php',
                'content'  => "<?php\n\nfunction greet(string \$name): string {\n    return \"Hello, {\$name}! Welcome to VisionLab.\";\n}\n\necho greet('World') . PHP_EOL;\n",
            ],
            [
                'id'       => 'readme-md',
                'name'     => 'README.md',
                'type'     => 'file',
                'language' => 'markdown',
                'content'  => "# My VisionLab Workspace\n\n> Powered by **VisionLab** — Aptech Vision 2026\n\n## Getting Started\n\n1. Select a file from the explorer on the left\n2. Edit in the Monaco editor\n3. Press `Ctrl + Enter` to execute\n4. Use the **AI Sidebar** for instant help\n\n## AI Agent Modes\n\n| Mode  | Capability |\n|-------|------------|\n| CHAT  | Ask questions, get explanations |\n| PLAN  | Step-by-step execution plan |\n| AGENT | Autonomous read/write with diff preview |\n",
            ],
        ];
    }

    public static function pistonLanguage(string $lang): array
    {
        return match ($lang) {
            'python'     => ['language' => 'python',     'version' => '3.10.0'],
            'javascript' => ['language' => 'javascript', 'version' => '18.15.0'],
            'typescript' => ['language' => 'typescript', 'version' => '5.0.3'],
            'php'        => ['language' => 'php',        'version' => '8.2.3'],
            'java'       => ['language' => 'java',       'version' => '15.0.2'],
            'c'          => ['language' => 'c',          'version' => '10.2.0'],
            'cpp'        => ['language' => 'c++',        'version' => '10.2.0'],
            'rust'       => ['language' => 'rust',       'version' => '1.50.0'],
            'go'         => ['language' => 'go',         'version' => '1.16.2'],
            'ruby'       => ['language' => 'ruby',       'version' => '3.0.1'],
            'bash'       => ['language' => 'bash',       'version' => '5.2.0'],
            default      => ['language' => 'python',     'version' => '3.10.0'],
        };
    }

    /** Personal workspace (no room) */
    public function index(Request $request)
    {
        $user  = Auth::user();
        
        $room = Room::firstOrCreate(
            ['slug' => 'personal-' . $user->id],
            [
                'name' => 'My Workspace',
                'owner_id' => $user->id,
                'language' => 'python',
                'is_public' => false,
                'max_participants' => 1,
            ]
        );

        $serverInfo = $this->codeServerManager->startWorkspace($room);

        return view('workspace', [
            'user'          => $user,
            'workspaceName' => 'My Workspace',
            'roomSlug'      => $room->slug,
            'isCollaborative' => false,
            'reverbConfig'  => $this->reverbConfig(),
            'vscodeUrl'     => $serverInfo['url'],
        ]);
    }

    /** Named collaborative room */
    public function show(Request $request, string $workspaceId)
    {
        $user  = Auth::user();

        // Try to load a real room
        $room = Room::where('slug', $workspaceId)->firstOrFail();
        
        $serverInfo = $this->codeServerManager->startWorkspace($room);

        return view('workspace', [
            'user'            => $user,
            'workspaceName'   => $room->name,
            'roomSlug'        => $room->slug,
            'isCollaborative' => true,
            'reverbConfig'    => $this->reverbConfig(),
            'vscodeUrl'       => $serverInfo['url'],
        ]);
    }

    private function reverbConfig(): array
    {
        // On Replit, the WebSocket server is reachable via the public dev domain on port 8080.
        // In production, REVERB_HOST/PORT/SCHEME should be set explicitly.
        $host   = env('REVERB_HOST', 'localhost');
        $port   = (int) env('REVERB_PORT', 8080);
        $scheme = env('REVERB_SCHEME', 'http');

        // Detect Replit environment — use the public dev domain so the browser
        // can reach the Reverb server from outside the container.
        if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.replit.dev')) {
            $host   = $_SERVER['HTTP_HOST'];
            $port   = 443;
            $scheme = 'https';
        } elseif ($envDomain = env('REPLIT_DEV_DOMAIN')) {
            $host   = $envDomain;
            $port   = 443;
            $scheme = 'https';
        }

        $key = env('REVERB_APP_KEY', 'VisionLab-key');

        return compact('key', 'host', 'port', 'scheme');
    }
}
