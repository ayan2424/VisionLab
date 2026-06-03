<?php

use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\CodeExecutionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\VideoRoomController;
use App\Http\Controllers\WorkspaceFileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Code Execution API
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:30,1'])
    ->prefix('code')->name('api.code.')->group(function () {
        Route::post('/run', [CodeExecutionController::class, 'run'])
            ->name('run');
        Route::get('/runtimes', [CodeExecutionController::class, 'runtimes'])
            ->name('runtimes');
    });

/*
|--------------------------------------------------------------------------
| Real-Time Collaboration API (Phase 4 — Reverb)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('rooms')->name('api.rooms.')->group(function () {
        Route::post('/{slug}/join', [RoomController::class, 'join'])
            ->name('join');
        Route::post('/{slug}/broadcast', [RoomController::class, 'broadcastCode'])
            ->name('broadcast');
        Route::post('/{slug}/cursor', [RoomController::class, 'broadcastCursor'])
            ->name('cursor');
    });

/*
|--------------------------------------------------------------------------
| Workspace File I/O API (Phase 2)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'throttle:60,1'])
    ->prefix('workspace/{slug}')->name('api.workspace.')->group(function () {
        Route::get('/files', [WorkspaceFileController::class, 'list'])
            ->name('files');
        Route::get('/read-file', [WorkspaceFileController::class, 'read'])
            ->name('read-file');
        Route::post('/write-file', [WorkspaceFileController::class, 'write'])
            ->name('write-file');
        Route::post('/create-file', [WorkspaceFileController::class, 'create'])
            ->name('create-file');
        Route::post('/delete-file', [WorkspaceFileController::class, 'delete'])
            ->name('delete-file');
        Route::post('/rename-file', [WorkspaceFileController::class, 'rename'])
            ->name('rename-file');
    });

/*
|--------------------------------------------------------------------------
| Video Conferencing API (Phase 6)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'throttle:30,1'])
    ->prefix('workspace/{slug}/video')->name('api.video.')->group(function () {
        Route::post('/start', [VideoRoomController::class, 'start'])
            ->name('start');
        Route::get('/status', [VideoRoomController::class, 'status'])
            ->name('status');
        Route::post('/end', [VideoRoomController::class, 'end'])
            ->name('end');
    });

/*
|--------------------------------------------------------------------------
| AI Agent API (Phase 4 - Continue Integration)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('ai/v1')->name('api.ai.')->group(function () {
        Route::post('/chat/completions', [AiController::class, 'chatCompletions'])
            ->name('chat.completions');
    });

Route::middleware(['auth:sanctum'])
    ->prefix('ai/patches')->name('api.ai.patches.')->group(function () {
        Route::post('/{patch}/approve', [AiController::class, 'approvePatch'])
            ->name('approve');
        Route::post('/{patch}/reject', [AiController::class, 'rejectPatch'])
            ->name('reject');
        Route::post('/execute-plan', [AiController::class, 'executePlan'])
            ->name('execute-plan');
    });

/*
|--------------------------------------------------------------------------
| AI Agent API (Phase 5 - Legacy UI Panel)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', 'throttle:10,1'])
    ->prefix('ai')->name('api.ai.legacy.')->group(function () {
        Route::post('/chat', [AiAgentController::class, 'chat'])
            ->name('chat');
        Route::post('/propose-patch', [AiAgentController::class, 'proposePatch'])
            ->name('propose-patch');
        Route::post('/apply-patch', [AiAgentController::class, 'applyPatch'])
            ->name('apply-patch');
        Route::post('/rollback', [AiAgentController::class, 'rollback'])
            ->name('rollback');
    });

/*
|--------------------------------------------------------------------------
| Health Check (Phase 9)
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $db = true;
    } catch (Exception $e) {
        $db = false;
    }

    return response()->json([
        'status' => $db ? 'healthy' : 'degraded',
        'database' => $db ? 'connected' : 'error',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
    ], $db ? 200 : 503);
});

// ── Push Notifications ──────────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('push')->name('push.')->group(function () {
    Route::post('/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('unsubscribe');
});

Route::get('/push/vapid-public-key', [PushSubscriptionController::class, 'vapidPublicKey'])->name('push.vapid');
