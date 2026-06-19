<?php
require '/www/wwwroot/visionlab/vendor/autoload.php';
$app = require_once '/www/wwwroot/visionlab/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$ws = \App\Models\Workspace::find(1);
try {
    $response = \Illuminate\Support\Facades\Http::timeout(2)->get("http://localhost:{$ws->port}/healthz");
    echo 'Successful: ' . ($response->successful() ? 'yes' : 'no') . "\n";
    echo 'Status: ' . $response->status() . "\n";
    echo 'Body: ' . $response->body() . "\n";
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
