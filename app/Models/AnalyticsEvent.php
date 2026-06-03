<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    protected $fillable = ['user_id', 'event_type', 'event_data'];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function track(string $eventType, array $data = [], ?int $userId = null): void
    {
        static::create([
            'user_id' => $userId ?? auth()->id(),
            'event_type' => $eventType,
            'event_data' => $data,
        ]);
    }
}
