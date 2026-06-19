<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$ws = \App\Models\Workspace::find(1);
if ($ws) {
    $ws->status = 'pending';
    $ws->save();
    echo "Reset workspace to pending\n";
} else {
    echo "Workspace 1 not found\n";
}
