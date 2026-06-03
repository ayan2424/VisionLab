<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionGraded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $studentId;

    public int $submissionId;

    public int $grade;

    public int $maxPoints;

    public string $assignmentTitle;

    public string $graderName;

    public ?string $feedback;

    public function __construct(Submission $submission)
    {
        $this->studentId = $submission->student_id;
        $this->submissionId = $submission->id;
        $this->grade = $submission->grade ?? 0;
        $this->maxPoints = $submission->assignment->max_points;
        $this->assignmentTitle = $submission->assignment->title;
        $this->graderName = $submission->grader?->name ?? 'Instructor';
        $this->feedback = $submission->feedback;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->studentId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'submission.graded';
    }

    public function broadcastWith(): array
    {
        return [
            'submission_id' => $this->submissionId,
            'grade' => $this->grade,
            'max_points' => $this->maxPoints,
            'assignment_title' => $this->assignmentTitle,
            'grader_name' => $this->graderName,
            'feedback' => $this->feedback,
        ];
    }
}
