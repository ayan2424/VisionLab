<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class DeploymentCompletedNotification extends Notification
{
    use Queueable;

    public $workspaceName;
    public $provider;
    public $status;
    public $url;

    public function __construct($workspaceName, $provider, $status, $url = null)
    {
        $this->workspaceName = $workspaceName;
        $this->provider = ucfirst($provider);
        $this->status = ucfirst($status);
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $body = "Your workspace {$this->workspaceName} deployment to {$this->provider} is {$this->status}.";
        
        return (new WebPushMessage)
            ->title('Deployment ' . $this->status)
            ->icon('/icon-192.png')
            ->body($body)
            ->action('View Deployments', 'view_deployments')
            ->data(['url' => $this->url ?? '/my-deployments']);
    }

    public function toArray($notifiable)
    {
        return [
            'workspace' => $this->workspaceName,
            'provider' => $this->provider,
            'status' => $this->status,
            'url' => $this->url,
        ];
    }
}
