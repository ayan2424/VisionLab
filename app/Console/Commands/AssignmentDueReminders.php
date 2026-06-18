<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignmentDueReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:due-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for assignments due in the next 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $assignments = \App\Models\Assignment::whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addHours(24)])
            ->with(['course.enrollments.student', 'submissions'])
            ->get();

        $count = 0;
        foreach ($assignments as $assignment) {
            foreach ($assignment->course->enrollments as $enrollment) {
                $student = $enrollment->student;
                $hasSubmitted = $assignment->submissions->where('student_id', $student->id)
                                    ->whereIn('status', ['submitted', 'graded', 'late'])->isNotEmpty();
                
                if (!$hasSubmitted) {
                    $student->notify(new \App\Notifications\AssignmentDueReminderNotification($assignment));
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} assignment due reminders.");
    }
}
