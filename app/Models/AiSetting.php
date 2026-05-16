<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class AiSetting extends Model
{
    protected $fillable = ['key', 'value', 'category', 'description'];

    /**
     * Get a setting value by key (decrypted if it's an API key).
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        return Cache::remember("ai_setting.{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting || !$setting->value) {
                return $default;
            }

            // Decrypt API keys
            if (str_contains($key, 'api_key')) {
                try {
                    return Crypt::decryptString($setting->value);
                } catch (\Exception $e) {
                    return $setting->value;
                }
            }

            return $setting->value;
        });
    }

    /**
     * Set a setting value (encrypts API keys).
     */
    public static function setValue(string $key, ?string $value, string $category = 'general', ?string $description = null): void
    {
        $storeValue = $value;
        if ($value && str_contains($key, 'api_key')) {
            $storeValue = Crypt::encryptString($value);
        }

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storeValue,
                'category' => $category,
                'description' => $description,
            ]
        );

        Cache::forget("ai_setting.{$key}");
    }

    /**
     * Get all settings by category.
     */
    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', $category)->get();
    }
}
