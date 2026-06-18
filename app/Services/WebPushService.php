<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:admin@visionlab.edu',
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
    }

    /**
     * Send a web push notification to a specific user.
     *
     * @param User $user
     * @param array $payload ['title' => '...', 'body' => '...', 'url' => '...']
     */
    public function sendToUser(User $user, array $payload)
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $sub) {
            $this->sendToSubscription($sub, $payload);
        }

        // Flush queued notifications
        $reports = $this->webPush->flush();
        $this->handleReports($reports);
    }

    /**
     * Send to a single subscription.
     */
    public function sendToSubscription(PushSubscription $sub, array $payload)
    {
        $subscription = Subscription::create([
            'endpoint' => $sub->endpoint,
            'publicKey' => $sub->p256dh_key,
            'authToken' => $sub->auth_token,
            'contentEncoding' => $sub->content_encoding,
        ]);

        $this->webPush->sendOneNotification($subscription, json_encode($payload));
    }

    /**
     * Handle delivery reports and delete expired subscriptions.
     */
    protected function handleReports($reports)
    {
        if (is_iterable($reports)) {
            foreach ($reports as $report) {
                if (!$report->isSuccess()) {
                    if ($report->isSubscriptionExpired()) {
                        $endpoint = $report->getRequest()->getUri()->__toString();
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    }
                }
            }
        }
    }
}
