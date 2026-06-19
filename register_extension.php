<?php
require '/www/wwwroot/visionlab/vendor/autoload.php';
$app = require_once '/www/wwwroot/visionlab/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Extension;
use Illuminate\Support\Facades\Storage;

$extPath = glob('/home/ubuntu/visionlab-agent/extensions/vscode/*.vsix');
if (empty($extPath)) {
    echo "VSIX not found!\n";
    exit(1);
}
$vsixFile = $extPath[0];
$filename = basename($vsixFile);
$hash = hash_file('sha256', $vsixFile);

$destPath = storage_path('app/extensions/' . $filename);
if (!is_dir(dirname($destPath))) {
    mkdir(dirname($destPath), 0755, true);
}
copy($vsixFile, $destPath);

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
