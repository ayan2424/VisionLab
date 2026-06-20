<?php
require '/www/wwwroot/visionlab/vendor/autoload.php';
$app = require_once '/www/wwwroot/visionlab/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Extension;
use Illuminate\Support\Facades\Storage;

$destPath = 'extensions/active/visionlab-collab-1.0.0.vsix';
$hash = 'local-vsix';

$extension = Extension::updateOrCreate(
    ['package_identifier' => 'visionlab-collab'],
    [
        'name' => 'VisionLab Collab',
        'version' => '1.0.0',
        'description' => 'Real-time collaboration, Jitsi video, and AI Patch reviewing for VisionLab workspaces.',
        'is_global' => true,
        'is_builtin' => true,
        'is_active' => true,
        'artifact_path' => $destPath,
        'checksum' => $hash,
        'rollout_state' => 'released',
    ]
);

echo "Extension registered successfully with ID: {$extension->id}\n";
