<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionForensic extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'human_keystrokes',
        'ai_injected_chars',
        'time_spent_seconds',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the percentage of human effort.
     */
    public function getHumanPercentageAttribute(): float
    {
        $total = $this->human_keystrokes + $this->ai_injected_chars;
        if ($total === 0) return 100.0;
        return round(($this->human_keystrokes / $total) * 100, 1);
    }
}
