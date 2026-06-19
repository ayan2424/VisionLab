<?php
require '/www/wwwroot/visionlab/vendor/autoload.php';
$app = require_once '/www/wwwroot/visionlab/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Extension;
use Illuminate\Support\Facades\Storage;

$destPath = 'extensions/active/visionlab.visionlab-agent-1.3.39';
$hash = 'local-dir';

$extension = Extension::updateOrCreate(
    ['package_identifier' => 'visionlab-agent'],
    [
        'name' => 'VisionLab Agent',
        'version' => '1.0.0',
        'description' => 'VisionLab Autonomous AI Agent',
        'is_global' => true,
        'is_builtin' => true,
        'is_active' => true,
        'artifact_path' => 'extensions/' . $filename,
        'checksum' => $hash,
        'rollout_state' => 'released',
    ]
);

echo "Extension registered successfully with ID: {$extension->id}\n";
