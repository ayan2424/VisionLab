<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class AssignmentGradedNotification extends Notification
{
    use Queueable;

    public $assignmentTitle;
    public $courseName;
    public $grade;

    public function __construct($assignmentTitle, $courseName, $grade)
    {
        $this->assignmentTitle = $assignmentTitle;
        $this->courseName = $courseName;
        $this->grade = $grade;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $body = "Your assignment '{$this->assignmentTitle}' in {$this->courseName} has been graded: {$this->grade}/100.";
        
        return (new WebPushMessage)
            ->title('Assignment Graded')
            ->icon('/icon-192.png')
            ->body($body)
            ->action('View Grade', 'view_grade')
            ->data(['url' => '/dashboard']);
    }

    public function toArray($notifiable)
    {
        return [
            'assignment' => $this->assignmentTitle,
            'course' => $this->courseName,
            'grade' => $this->grade,
        ];
    }
}
