<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ws = \App\Models\Workspace::find(1);
if (!$ws) die("Workspace not found");
$ws->status = 'pending';
$ws->save();

$manager = app(\App\Services\CodeServerManager::class);
$result = $manager->startWorkspace($ws);
print_r($result);
