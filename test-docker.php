<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$manager = app(\App\Services\CodeServerManager::class);
echo 'Docker available: ' . ($manager->isDockerAvailable() ? 'yes' : 'no') . "\n";
$process = new \Symfony\Component\Process\Process(['/usr/bin/docker', 'info']);
$process->run();
echo '<pre>' . htmlspecialchars($process->getErrorOutput() . "\n" . $process->getOutput()) . '</pre>';
