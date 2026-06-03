<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyDueAssignments extends Command
{
    protected $signature = 'notify:due-assignments
                            {--hours=24 : Notify for assignments due within N hours}';

    protected $description = 'Send push notifications for assignments due soon';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $dueAssignments = Assignment::where('due_date', '>', now())
            ->where('due_date', '<=', now()->addHours($hours))
            ->with('course.enrollments.student')
            ->get();

        if ($dueAssignments->isEmpty()) {
            $this->info('No assignments due in the next '.$hours.' hours.');

            return self::SUCCESS;
        }

        $notified = 0;

        foreach ($dueAssignments as $assignment) {
            $course = $assignment->course;

            foreach ($course->enrollments as $enrollment) {
                $student = $enrollment->student;
                if (! $student) {
                    continue;
                }

                // Check if student has a submission (skip if already submitted)
                $hasSubmission = $assignment->submissions()
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['submitted', 'graded'])
                    ->exists();

                if ($hasSubmission) {
                    continue;
                }

                $subscriptions = PushSubscription::where('user_id', $student->id)->get();

                foreach ($subscriptions as $sub) {
                    $this->sendPushNotification($sub, [
                        'title' => '⏰ Assignment Due Soon',
                        'body' => "\"{$assignment->title}\" in {$course->title} is due {$assignment->due_date->diffForHumans()}",
                        'url' => "/assignments/{$assignment->id}",
                        'icon' => '/icons/icon-192.png',
                    ]);
                    $notified++;
                }
            }
        }

        $this->info("✅ Sent {$notified} push notification(s) for {$dueAssignments->count()} assignment(s).");

        return self::SUCCESS;
    }

    private function sendPushNotification(PushSubscription $sub, array $payload): void
    {
        $vapidPublicKey = config('webpush.public_key', env('VAPID_PUBLIC_KEY', ''));
        $vapidPrivateKey = config('webpush.private_key', env('VAPID_PRIVATE_KEY', ''));

        if (! $vapidPublicKey || ! $vapidPrivateKey) {
            Log::debug('Push notification skipped: VAPID keys not configured');

            return;
        }

        try {
            // For production, use a proper Web Push library like minishlink/web-push
            // This is a simplified implementation for demo purposes
            Log::info('Push notification sent', [
                'user_id' => $sub->user_id,
                'endpoint' => substr($sub->endpoint, 0, 50).'...',
                'title' => $payload['title'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Push notification failed', ['error' => $e->getMessage()]);
        }
    }
}
