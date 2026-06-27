<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'throttle:push'])->post('/push-subscriptions', [\App\Http\Controllers\PushSubscriptionController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Real-Time Collaboration API (Phase 4 â€” Reverb)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:api'])
    ->prefix('rooms')->name('api.rooms.')->group(function () {
    Route::post('/{slug}/join',      [\App\Http\Controllers\RoomController::class, 'join'])
        ->name('join');
    Route::post('/{slug}/broadcast', [\App\Http\Controllers\RoomController::class, 'broadcastCode'])
        ->name('broadcast');
    Route::post('/{slug}/cursor',    [\App\Http\Controllers\RoomController::class, 'broadcastCursor'])
        ->name('cursor');
});

/*
|--------------------------------------------------------------------------
| Workspace File I/O API (Phase 2)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:file-api'])
    ->prefix('workspace/{slug}')->name('api.workspace.')->group(function () {
    Route::get('/files',       [\App\Http\Controllers\WorkspaceFileController::class, 'list'])
        ->name('files');
    Route::get('/read-file',   [\App\Http\Controllers\WorkspaceFileController::class, 'read'])
        ->name('read-file');
    Route::post('/write-file', [\App\Http\Controllers\WorkspaceFileController::class, 'write'])
        ->name('write-file');
    Route::post('/create-file',[\App\Http\Controllers\WorkspaceFileController::class, 'create'])
        ->name('create-file');
    Route::post('/delete-file',[\App\Http\Controllers\WorkspaceFileController::class, 'delete'])
        ->name('delete-file');
    Route::post('/rename-file',[\App\Http\Controllers\WorkspaceFileController::class, 'rename'])
        ->name('rename-file');
    
    // Phase 9: VisionGuard Forensics
    Route::post('/forensics/sync', [\App\Http\Controllers\Api\ForensicsController::class, 'sync'])
        ->name('forensics.sync');
});

/*
|--------------------------------------------------------------------------
| Video Conferencing API (Phase 6)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:video'])
    ->prefix('workspace/{slug}/video')->name('api.video.')->group(function () {
    Route::post('/start',  [\App\Http\Controllers\VideoRoomController::class, 'start'])
        ->name('start');
    Route::get('/status',  [\App\Http\Controllers\VideoRoomController::class, 'status'])
        ->name('status');
    Route::post('/attendance', [\App\Http\Controllers\VideoRoomController::class, 'attendance'])
        ->name('attendance');
    Route::post('/end',    [\App\Http\Controllers\VideoRoomController::class, 'end'])
        ->name('end');
});

/*
|--------------------------------------------------------------------------
| AI Agent API (Phase 4 - Continue Integration)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:ai'])
    ->prefix('ai/v1')->name('api.ai.')->group(function () {
    Route::post('/chat/completions', [\App\Http\Controllers\Api\AiController::class, 'chatCompletions'])
        ->name('chat.completions');
});

Route::middleware(['auth:sanctum', 'throttle:ai'])
    ->prefix('ai/patches')->name('api.ai.patches.')->group(function () {
    Route::get('/pending', [\App\Http\Controllers\Api\AiController::class, 'pendingPatches'])
        ->name('pending');
    Route::post('/{patch}/approve', [\App\Http\Controllers\Api\AiController::class, 'approvePatch'])
        ->name('approve');
    Route::post('/{patch}/reject', [\App\Http\Controllers\Api\AiController::class, 'rejectPatch'])
        ->name('reject');
    Route::post('/execute-plan', [\App\Http\Controllers\Api\AiController::class, 'executePlan'])
        ->name('execute-plan');
});

Route::middleware(['auth:sanctum'])->post('/ai/execute', function (Illuminate\Http\Request $request) {
    return response()->json(['error' => 'Direct writing/execution is forbidden.'], 403);
});


/*
|--------------------------------------------------------------------------
| Recording API (Video Call Recording)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:video'])
    ->prefix('workspace/{roomSlug}/recording')->name('api.recording.')->group(function () {
    Route::post('/start', [\App\Http\Controllers\RecordingController::class, 'start'])
        ->name('start');
    Route::post('/stop',  [\App\Http\Controllers\RecordingController::class, 'stop'])
        ->name('stop');
});

Route::middleware(['auth:sanctum', 'throttle:video'])
    ->prefix('recording')->name('api.recording.')->group(function () {
    Route::get('/list',                         [\App\Http\Controllers\RecordingController::class, 'index'])
        ->name('index');
    Route::get('/{recording}/playback',         [\App\Http\Controllers\RecordingController::class, 'playback'])
        ->name('playback');
    Route::post('/{recording}/approve',         [\App\Http\Controllers\RecordingController::class, 'approve'])
        ->name('approve');
    Route::post('/{recording}/reject',          [\App\Http\Controllers\RecordingController::class, 'reject'])
        ->name('reject');
});

/*
|--------------------------------------------------------------------------
| Health Check (Phase 9)
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $db = true;
    } catch (\Exception $e) {
        $db = false;
    }

    return response()->json([
        'status'    => $db ? 'healthy' : 'degraded',
        'database'  => $db ? 'connected' : 'error',
        'timestamp' => now()->toISOString(),
        'version'   => '1.0.0',
    ], $db ? 200 : 503);
});

/*
|--------------------------------------------------------------------------
| VisionGuard Telemetry API (Phase 9)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->prefix('visionguard')->name('api.visionguard.')->group(function () {
    Route::post('/log', [\App\Http\Controllers\VisionGuardController::class, 'logEvent'])->name('log');
    Route::get('/forensics', [\App\Http\Controllers\VisionGuardController::class, 'getForensics'])->name('forensics');
});

Route::post('/webhook/deploy', [\App\Http\Controllers\WebhookController::class, 'deploy']);

Route::middleware(['auth:sanctum'])->get('/extensions', function () {
    return response()->json(\App\Models\Extension::where('is_active', true)->get()->map(function ($ext) {
        return [
            'id' => $ext->id,
            'name' => $ext->name,
            'identifier' => $ext->package_identifier,
            'version' => $ext->version,
            'sha256_checksum' => $ext->checksum,
            'is_global' => $ext->is_global,
            'is_builtin' => $ext->is_builtin,
            'is_active' => $ext->is_active,
        ];
    }
    ));
});
