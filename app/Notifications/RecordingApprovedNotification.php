+<?php
+namespace App\Notifications;
+
+use App\Models\Recording;
+use Illuminate\Notifications\Messages\MailMessage;
+use Illuminate\Notifications\Notification;
+
+class RecordingApprovedNotification extends Notification
+{
+    public function __construct(public readonly Recording $recording) {}

+    public function via(object $notifiable): array { return ['mail', 'database']; }
+
+    public function toMail(object $notifiable): MailMessage
+    {
+        return (new MailMessage)
+            ->subject('Your Recording Has Been Approved')
+            ->line("The recording from room [{$this->recording->room_slug}] has been approved.")
+            ->action('Watch Recording', url("/recordings/{$this->recording->id}/playback"))
+            ->line('The link will expire after 60 minutes.');
+    }
+
+    public function toArray(object $notifiable): array
+    {
+        return [
+            'recording_id' => $this->recording->id,
+            'action'       => 'recording_approved',
+        ];
+    }
+}