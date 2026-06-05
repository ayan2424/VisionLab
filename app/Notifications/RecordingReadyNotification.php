+<?php
+namespace App\Notifications;
+
+use App\Models\Recording;
+use Illuminate\Notifications\Messages\MailMessage;
+use Illuminate\Notifications\Notification;
+
+class RecordingReadyNotification extends Notification
+{
+    public function __construct(public readonly Recording $recording) {}
+
+    public function via(object $notifiable): array { return ['mail', 'database']; }
+
+    public function toMail(object $notifiable): MailMessage
+    {
+        return (new MailMessage)
+            ->subject('New Recording Pending Approval')
+            ->line("A recording from room [{$this->recording->room_slug}] is ready for review.")
+            ->line("Duration: {$this->recording->duration_seconds}s")
+            ->action('Review Recording', url("/admin/recordings/{$this->recording->id}"))
+            ->line('Please approve or reject within 48 hours.');
+    }
+
+    public function toArray(object $notifiable): array
+    {
+        return [
+            'recording_id' => $this->recording->id,
+            'room_slug'    => $this->recording->room_slug,
+            'action'       => 'recording_pending_approval',
+        ];
+    }
+}