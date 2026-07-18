<?php

namespace App\Observers;

class AnalyticsObserver
{
    public function created($model)
    {
        $this->logEvent($model, 'created');
    }

    public function updated($model)
    {
        $this->logEvent($model, 'updated');
    }

    protected function logEvent($model, string $action)
    {
        $userId = null;
        if ($model instanceof \App\Models\User) $userId = $model->id;
        elseif (isset($model->user_id)) $userId = $model->user_id;
        elseif (isset($model->student_id)) $userId = $model->student_id;

        if ($userId) {
            $eventType = strtolower(class_basename($model)) . '_' . $action;
            
            \App\Models\AnalyticsEvent::create([
                'user_id' => $userId,
                'event_type' => $eventType,
                'event_data' => $model->getChanges() ?: $model->toArray(),
                'resource_type' => get_class($model),
                'resource_id' => $model->id,
            ]);

            // Dispatch to GamificationService if it exists
            if (class_exists(\App\Services\GamificationService::class)) {
                app(\App\Services\GamificationService::class)->evaluateBadges(
                    \App\Models\User::find($userId)
                );
            }
        }
    }
}
