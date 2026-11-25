<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct(?WebPush $webPush = null)
    {
        if ($webPush !== null) {
            $this->webPush = $webPush;
            return;
        }

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
     * Send a web push notification to all registered devices for a specific user.
     *
     * @param  array{title?: string, body?: string, url?: string}  $payload
     */
    public function sendToUser(User $user, array $payload): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $encodedPayload = json_encode($payload);

        foreach ($subscriptions as $sub) {
            $subscription = $this->buildSubscription($sub);
            $this->webPush->queueNotification($subscription, $encodedPayload ?: '{}');
        }

        // Flush all queued notifications in a single batch and prune expired endpoints
        $reports = $this->webPush->flush();
        $this->handleReports($reports);
    }

    /**
     * Send immediately to a single subscription and handle expiry reporting.
     *
     * @param  array{title?: string, body?: string, url?: string}  $payload
     */
    public function sendToSubscription(PushSubscription $sub, array $payload): MessageSentReport
    {
        $subscription = $this->buildSubscription($sub);
        $report = $this->webPush->sendOneNotification($subscription, json_encode($payload) ?: '{}');
        $this->handleReports([$report]);

        return $report;
    }

    protected function buildSubscription(PushSubscription $sub): Subscription
    {
        return Subscription::create([
            'endpoint' => $sub->endpoint,
            'publicKey' => $sub->p256dh_key,
            'authToken' => $sub->auth_token,
            'contentEncoding' => $sub->content_encoding,
        ]);
    }

    /**
     * Handle delivery reports and delete expired subscriptions.
     */
    protected function handleReports(iterable $reports): void
    {
        foreach ($reports as $report) {
            if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                PushSubscription::where('endpoint', $endpoint)->delete();
            }
        }
    }
}
