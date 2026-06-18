<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assignment;
use App\Services\WebPushService;

class SendDueRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visionlab:due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notifications for assignments due in 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(WebPushService $webPush)
    {
        $assignments = Assignment::where('due_date', '>', now())
            ->where('due_date', '<=', now()->addHours(24))
            ->get();

        $count = 0;
        foreach ($assignments as $assignment) {
            $students = $assignment->course->students;
            foreach ($students as $student) {
                // Check if they already submitted
                $hasSubmitted = $assignment->submissions()
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['submitted', 'graded', 'late'])
                    ->exists();

                if (!$hasSubmitted) {
                    $webPush->sendToUser($student, [
                        'title' => 'Assignment Due Soon',
                        'body' => "{$assignment->title} is due in less than 24 hours!",
                        'url' => route('assignments.show', $assignment->id),
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} due reminder push notifications.");
    }
}
