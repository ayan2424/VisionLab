<?php

/**
 * VisionLab Hybrid Extension Rebranding Pipeline: Strategy B
 * Rapid Compiled-Level Rebranding for Heavy Tools (Prettier, GitLens)
 */

if ($argc < 2) {
    die("Usage: php rebrand_compiled.php <path_to_extension.vsix> [new_name] [new_display_name]\n");
}

$vsixPath = $argv[1];
$newName = $argv[2] ?? 'visionlab-tool';
$newDisplayName = $argv[3] ?? 'VisionLab Tool';

if (!file_exists($vsixPath)) {
    die("❌ Error: File not found: $vsixPath\n");
}

echo "🚀 Starting Rapid Rebranding for $vsixPath...\n";

// 1. Unzip to temporary directory
$tempDir = sys_get_temp_dir() . '/vl_rebrand_' . uniqid();
mkdir($tempDir);

$zip = new ZipArchive();
if ($zip->open($vsixPath) === TRUE) {
    $zip->extractTo($tempDir);
    $zip->close();
} else {
    die("❌ Error: Failed to extract VSIX.\n");
}

// 2. Modify package.json
$packageJsonPath = $tempDir . '/extension/package.json';
if (!file_exists($packageJsonPath)) {
    // Some VSIX might have different structure, usually it's in /extension/
    die("❌ Error: package.json not found in /extension/ directory.\n");
}

$pkg = json_decode(file_get_contents($packageJsonPath), true);

$oldName = $pkg['name'] ?? 'unknown';
$oldDisplayName = $pkg['displayName'] ?? 'unknown';

echo "💉 Replacing '$oldDisplayName' ($oldName) -> '$newDisplayName' ($newName)...\n";

$pkg['name'] = $newName;
$pkg['displayName'] = $newDisplayName;
$pkg['publisher'] = 'VisionLab';
$pkg['description'] = "VisionLab locked-down version of {$oldDisplayName}";

if (isset($pkg['author'])) {
    if (is_array($pkg['author'])) {
        $pkg['author']['name'] = 'VisionLab Team';
    } else {
        $pkg['author'] = 'VisionLab Team';
    }
}

file_put_contents($packageJsonPath, json_encode($pkg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// 3. Re-zip into new VSIX
$newVsixPath = dirname($vsixPath) . '/' . $newName . '.vsix';
if (file_exists($newVsixPath)) {
    unlink($newVsixPath);
}

$newZip = new ZipArchive();
if ($newZip->open($newVsixPath, ZipArchive::CREATE) === TRUE) {
    
    // Recursive zip
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($tempDir) + 1);
            $newZip->addFile($filePath, $relativePath);
        }
    }
    
    $newZip->close();
} else {
    die("❌ Error: Failed to create new VSIX.\n");
}

// Cleanup
array_map('unlink', glob("$tempDir/*.*"));
exec("rm -rf " . escapeshellarg($tempDir));

echo "✅ Successfully created rebranded VSIX: $newVsixPath\n";
