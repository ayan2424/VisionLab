<?php

echo "Starting Continue Extension Rebranding process...\n";

$downloadUrl = 'https://open-vsx.org/api/Continue/continue/linux-x64/1.3.38/file/Continue.continue-1.3.38@linux-x64.vsix';
$vsixFile = __DIR__ . '/continue.vsix';
$extractDir = __DIR__ . '/extracted_continue';
$outputVsix = __DIR__ . '/visionlab-ai.vsix';

if (!file_exists($vsixFile)) {
    echo "Downloading Continue extension...\n";
    $content = file_get_contents($downloadUrl);
    if ($content === false) {
        die("Failed to download the extension.\n");
    }
    file_put_contents($vsixFile, $content);
    echo "Downloaded to {$vsixFile}\n";
} else {
    echo "Continue extension already downloaded.\n";
}

if (is_dir($extractDir)) {
    echo "Cleaning up old extraction directory...\n";
    // recursive delete
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($extractDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    rmdir($extractDir);
}

echo "Extracting .vsix...\n";
$zip = new ZipArchive;
if ($zip->open($vsixFile) === TRUE) {
    $zip->extractTo($extractDir);
    $zip->close();
    echo "Extraction complete.\n";
} else {
    die("Failed to extract the VSIX archive.\n");
}

echo "Modifying package.json...\n";
$packageJsonPath = $extractDir . '/extension/package.json';
if (!file_exists($packageJsonPath)) {
    die("package.json not found in the extracted archive.\n");
}

$packageJson = json_decode(file_get_contents($packageJsonPath), true);

// Perform rebranding
$packageJson['name'] = 'visionlab-ai';
$packageJson['displayName'] = 'VisionLab Agent';
$packageJson['publisher'] = 'VisionLab';
$packageJson['description'] = 'VisionLab AI Coding Assistant';
$packageJson['icon'] = 'extension/icon.png';

// Change configuration defaults if necessary, though we inject config.json anyway
// Write back the modified JSON
file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "package.json modified successfully.\n";

// Repack the extension
echo "Repacking to visionlab-ai.vsix...\n";
if (file_exists($outputVsix)) {
    unlink($outputVsix);
}

$newZip = new ZipArchive;
if ($newZip->open($outputVsix, ZipArchive::CREATE) === TRUE) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($extractDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($extractDir) + 1);
            $relativePath = str_replace('\\', '/', $relativePath);
            $newZip->addFile($filePath, $relativePath);
        }
    }
    $newZip->close();
    echo "Successfully repacked to {$outputVsix}\n";
} else {
    die("Failed to create the new VSIX archive.\n");
}

echo "Rebranding complete!\n";
