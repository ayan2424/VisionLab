<?php

namespace Tests\Feature;

use App\Jobs\ProcessRecordingJob;
use App\Models\Recording;
use App\Models\User;
use App\Notifications\RecordingApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * RecordingControllerTest — Verifies video recording start, stop,
 * approval, rejection, and playback access control features.
 */
class RecordingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_recording(): void
    {
        $user = User::factory()->student()->create();

        $response = $this->actingAs($user)->post(route('api.recording.start', 'room-slug-123'), [
            'room_name' => 'visioncode-room-123',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['recording_id']);

        $recordingId = $response->json('recording_id');

        $this->assertDatabaseHas('recordings', [
            'id'        => $recordingId,
            'room_slug' => 'room-slug-123',
            'user_id'   => $user->id,
            'status'    => 'recording',
        ]);

        $this->assertDatabaseHas('recording_audit_logs', [
            'recording_id' => $recordingId,
            'user_id'      => $user->id,
            'action'       => 'started',
        ]);
    }

    public function test_user_can_stop_recording(): void
    {
        Queue::fake();

        $user = User::factory()->student()->create();
        $recording = Recording::create([
            'room_slug'  => 'room-slug-123',
            'user_id'    => $user->id,
            'status'     => 'recording',
            'started_at' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($user)->post(route('api.recording.stop', 'room-slug-123'), [
            'recording_id' => $recording->id,
        ]);

        $response->assertOk()
                 ->assertJsonPath('status', 'processing');

        $this->assertDatabaseHas('recordings', [
            'id'     => $recording->id,
            'status' => 'processing',
        ]);

        $recording->refresh();
        $this->assertNotNull($recording->ended_at);
        $this->assertGreaterThan(0, $recording->duration_seconds);

        Queue::assertPushed(ProcessRecordingJob::class, function ($job) use ($recording) {
            return $job->recordingId === $recording->id;
        });

        $this->assertDatabaseHas('recording_audit_logs', [
            'recording_id' => $recording->id,
            'user_id'      => $user->id,
            'action'       => 'stopped',
        ]);
    }

    public function test_admin_can_list_recordings(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();

        Recording::create([
            'room_slug'  => 'room-slug-123',
            'user_id'    => $student->id,
            'status'     => 'recording',
            'started_at' => now(),
        ]);

        // Student cannot list
        $this->actingAs($student)->get(route('api.recording.index'))
             ->assertStatus(403);

        // Admin can list
        $response = $this->actingAs($admin)->get(route('api.recording.index'));
        $response->assertOk()
                 ->assertJsonStructure(['data']);
    }

    public function test_admin_can_approve_recording(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $recording = Recording::create([
            'room_slug'  => 'room-slug-123',
            'user_id'    => $student->id,
            'status'     => 'processing',
            'started_at' => now(),
        ]);

        // Student cannot approve
        $this->actingAs($student)->post(route('api.recording.approve', $recording->id))
             ->assertStatus(403);

        // Admin can approve
        $response = $this->actingAs($admin)->post(route('api.recording.approve', $recording->id));
        $response->assertOk()
                 ->assertJsonPath('status', 'approved');

        $this->assertDatabaseHas('recordings', [
            'id'          => $recording->id,
            'status'      => 'approved',
            'approved_by' => $admin->id,
        ]);

        Notification::assertSentTo($student, RecordingApprovedNotification::class);

        $this->assertDatabaseHas('recording_audit_logs', [
            'recording_id' => $recording->id,
            'user_id'      => $admin->id,
            'action'       => 'approved',
        ]);
    }

    public function test_admin_can_reject_recording(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $recording = Recording::create([
            'room_slug'  => 'room-slug-123',
            'user_id'    => $student->id,
            'status'     => 'processing',
            'started_at' => now(),
        ]);

        // Student cannot reject
        $this->actingAs($student)->post(route('api.recording.reject', $recording->id), [
            'reason' => 'inappropriate content',
        ])->assertStatus(403);

        // Admin can reject
        $response = $this->actingAs($admin)->post(route('api.recording.reject', $recording->id), [
            'reason' => 'inappropriate content',
        ]);
        $response->assertOk()
                 ->assertJsonPath('status', 'rejected');

        $this->assertDatabaseHas('recordings', [
            'id'               => $recording->id,
            'status'           => 'rejected',
            'rejection_reason' => 'inappropriate content',
        ]);

        $this->assertDatabaseHas('recording_audit_logs', [
            'recording_id' => $recording->id,
            'user_id'      => $admin->id,
            'action'       => 'rejected',
        ]);
    }

    public function test_recording_playback_access_control(): void
    {
        $owner = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();
        $admin = User::factory()->admin()->create();

        $recording = Recording::create([
            'room_slug'    => 'room-slug-123',
            'user_id'      => $owner->id,
            'status'       => 'processing',
            'storage_path' => 'recordings/test.mp4',
            'started_at'   => now(),
        ]);

        // Mock Storage temporaryUrl
        Storage::shouldReceive('temporaryUrl')
            ->andReturn('https://s3.amazonaws.com/my-bucket/recordings/test.mp4');

        // 1. Playback when status is processing/not-approved
        // Owner can access
        $this->actingAs($owner)->get(route('api.recording.playback', $recording->id))
             ->assertOk()
             ->assertJsonStructure(['url', 'expires_in']);

        // Admin can access
        $this->actingAs($admin)->get(route('api.recording.playback', $recording->id))
             ->assertOk();

        // Other student cannot access
        $this->actingAs($otherStudent)->get(route('api.recording.playback', $recording->id))
             ->assertStatus(403);

        // 2. Playback when status is approved
        $recording->update(['status' => 'approved']);

        // Other student can now access
        $this->actingAs($otherStudent)->get(route('api.recording.playback', $recording->id))
             ->assertOk();
    }
}
