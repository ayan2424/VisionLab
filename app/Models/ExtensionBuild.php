<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExtensionBuild — Records the provenance and build pipeline for an extension artifact.
 * Critical for VisionLab Agent: tracks source audit, branding changes, and license review.
 */
class ExtensionBuild extends Model
{
    protected $fillable = [
        'extension_id', 'source_repository', 'source_reference',
        'build_strategy', 'branding_changes_summary', 'configuration_changes_summary',
        'artifact_checksum', 'license_review_status', 'compatibility_status',
        'build_logs_location', 'built_by', 'built_at',
    ];

    protected function casts(): array
    {
        return ['built_at' => 'datetime'];
    }

    public function extension(): BelongsTo
    {
        return $this->belongsTo(Extension::class);
    }

    public function builder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'built_by');
    }

    public function isLicenseApproved(): bool
    {
        return $this->license_review_status === 'approved';
    }

    public function isCompatible(): bool
    {
        return $this->compatibility_status === 'compatible';
    }
}
