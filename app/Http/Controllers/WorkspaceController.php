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
        $files = config('workspace.default_files', []);

        foreach ($files as &$file) {
            if (isset($file['content'])) {
                $file['content'] = str_replace('{{name}}', $name, $file['content']);
            }
        }

        return $files;
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
