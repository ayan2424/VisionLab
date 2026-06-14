<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRecordingJob;
use App\Models\Recording;
use App\Models\RecordingAuditLog;
use App\Notifications\RecordingApprovedNotification;
use App\Notifications\RecordingReadyNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class RecordingController extends Controller
{
    // POST /api/workspace/{roomSlug}/recording/start
    public function start(Request $request, string $roomSlug): JsonResponse
    {
        $user = $request->user();

        $recording = Recording::create([
            'room_slug'  => $roomSlug,
            'user_id'    => $user->id,
            'status'     => 'recording',
            'started_at' => now(),
            'metadata'   => ['jitsi_room' => $request->input('room_name')],
        ]);

        $this->audit($recording, $user, 'started', $request);

        return response()->json(['recording_id' => $recording->id]);
    }

    // POST /api/workspace/{roomSlug}/recording/stop
    public function stop(Request $request, string $roomSlug): JsonResponse
    {
        $user      = $request->user();
        $recording = Recording::where('id', $request->input('recording_id'))
                               ->where('room_slug', $roomSlug)
                               ->firstOrFail();

        $ended = now();
        $recording->update([
            'status'           => 'processing',
            'ended_at'         => $ended,
            'duration_seconds' => (int) abs($ended->diffInSeconds($recording->started_at)),
        ]);

        $this->audit($recording, $user, 'stopped', $request);

        // Dispatch async processing job
        ProcessRecordingJob::dispatch($recording->id);

        return response()->json(['status' => 'processing']);
    }

    // POST /api/recording/{recording}/approve  (admin only)
    public function approve(Request $request, Recording $recording): JsonResponse
    {
        Gate::authorize('approve', $recording);

        $recording->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->audit($recording, $request->user(), 'approved', $request);

        // Notify instructor + admins
        $recording->user->notify(new RecordingApprovedNotification($recording));

        return response()->json(['status' => 'approved']);
    }

    // POST /api/recording/{recording}/reject  (admin only)
    public function reject(Request $request, Recording $recording): JsonResponse
    {
        Gate::authorize('approve', $recording);

        $recording->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('reason'),
        ]);

        $this->audit($recording, $request->user(), 'rejected', $request);

        return response()->json(['status' => 'rejected']);
    }

    // GET /api/recording/{recording}/playback
    public function playback(Request $request, Recording $recording): JsonResponse
    {
        $user = $request->user();

        if (!$recording->isAccessibleBy($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if (!$recording->storage_path) {
            return response()->json(['error' => 'Recording not yet available'], 404);
        }

        $this->audit($recording, $user, 'played', $request);

        $url = $recording->generateSignedUrl(60);

        return response()->json(['url' => $url, 'expires_in' => 3600]);
    }

    // GET /api/recordings  (admin listing)
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Recording::class);

        $recordings = Recording::with(['user','approver'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->room_slug, fn($q, $r) => $q->where('room_slug', $r))
            ->latest()
            ->paginate(20);

        return response()->json($recordings);
    }

    private function audit(Recording $recording, User $user, string $action, Request $request, array $context = []): void
    {
        RecordingAuditLog::create([
            'recording_id' => $recording->id,
            'user_id'      => $user->id,
            'action'       => $action,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'context'      => $context ?: null,
        ]);
    }
}