<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SubmissionForensic — VisionGuard data for human vs AI contribution tracking.
 * Provides confidence-rated classification of code authorship.
 */
class SubmissionForensic extends Model
{
    protected $fillable = [
        'submission_id', 'workspace_id', 'human_keystrokes',
        'ai_patches_applied', 'pasted_count', 'imported_count',
        'human_pct', 'ai_pct', 'confidence', 'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'human_keystrokes'  => 'integer',
            'ai_patches_applied' => 'integer',
            'pasted_count'      => 'integer',
            'imported_count'    => 'integer',
            'human_pct'         => 'decimal:2',
            'ai_pct'            => 'decimal:2',
            'last_synced_at'    => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Recalculate percentages from raw counters.
     */
    public function recalculate(): void
    {
        $total = $this->human_keystrokes + $this->ai_patches_applied
            + $this->pasted_count + $this->imported_count;

        if ($total > 0) {
            $this->human_pct = round(($this->human_keystrokes / $total) * 100, 2);
            $this->ai_pct    = round(($this->ai_patches_applied / $total) * 100, 2);
        } else {
            $this->human_pct = 100.00;
            $this->ai_pct    = 0.00;
        }

        $this->last_synced_at = now();
        $this->save();
    }
}
