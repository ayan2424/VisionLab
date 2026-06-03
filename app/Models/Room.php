<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'owner_id',
        'language',
        'is_public',
        'max_participants',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'max_participants' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(RoomMember::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public static function generateSlug(): string
    {
        do {
            $slug = Str::lower(Str::random(8));
        } while (self::where('slug', $slug)->exists());

        return $slug;
    }

    public function isMember(User $user): bool
    {
        return $this->owner_id === $user->id
            || $this->members()->where('user_id', $user->id)->exists();
    }

    public function getPresenceColor(int $userId): string
    {
        $colors = ['#7c3aed', '#2563eb', '#0891b2', '#16a34a', '#dc2626', '#d97706', '#db2777'];

        return $colors[$userId % count($colors)];
    }
}
