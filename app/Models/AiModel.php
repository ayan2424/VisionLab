<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    protected $fillable = [
        'provider',
        'model_id',
        'display_name',
        'role',
        'is_default',
        'is_active',
        'context_length',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Get all active models for a specific role.
     */
    public static function getActiveByRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('role', $role)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Get the default model for a role.
     */
    public static function getDefaultForRole(string $role): ?self
    {
        return static::where('role', $role)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first()
            ?? static::where('role', $role)->where('is_active', true)->first();
    }

    /**
     * Get all active chat models (for the extension sidebar dropdown).
     */
    public static function getActiveChatModels(): \Illuminate\Database\Eloquent\Collection
    {
        return static::whereIn('role', ['chat', 'agent'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the autocomplete model.
     */
    public static function getAutocompleteModel(): ?self
    {
        return static::getDefaultForRole('autocomplete');
    }

    /**
     * Set a model as default for its role (unset others).
     */
    public function makeDefault(): void
    {
        static::where('role', $this->role)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Get the API key for this model's provider.
     */
    public function getApiKey(): ?string
    {
        return AiSetting::getValue("{$this->provider}_api_key");
    }
}
