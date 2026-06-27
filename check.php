<?php
require '/www/wwwroot/visionlab.ayan24.me/vendor/autoload.php';
$app = require_once '/www/wwwroot/visionlab.ayan24.me/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$workspaces = \App\Models\Workspace::all();
foreach ($workspaces as $ws) {
    echo "ID: {$ws->id}, Port: {$ws->port}, ProxyUrl: {$ws->proxy_url}\n";
}
