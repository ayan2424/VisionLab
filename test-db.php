<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$ws = \App\Models\Workspace::find(1);
echo "PORT: " . $ws->port . "\n";
echo "STATUS: " . $ws->status . "\n";
