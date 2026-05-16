<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AiSetting;
use Illuminate\Database\Seeder;

class AiModelsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Default API Key Placeholders ──
        $settings = [
            ['key' => 'anthropic_api_key', 'category' => 'api_keys', 'description' => 'Anthropic (Claude) API Key'],
            ['key' => 'google_api_key', 'category' => 'api_keys', 'description' => 'Google AI (Gemini) API Key'],
            ['key' => 'openai_api_key', 'category' => 'api_keys', 'description' => 'OpenAI (GPT) API Key'],
            ['key' => 'deepseek_api_key', 'category' => 'api_keys', 'description' => 'DeepSeek API Key'],
            ['key' => 'openrouter_api_key', 'category' => 'api_keys', 'description' => 'OpenRouter API Key (Multi-provider)'],
            ['key' => 'autocomplete_enabled', 'value' => 'true', 'category' => 'features', 'description' => 'Enable Tab Autocomplete'],
            ['key' => 'indexing_enabled', 'value' => 'false', 'category' => 'features', 'description' => 'Enable Codebase Indexing'],
            ['key' => 'telemetry_enabled', 'value' => 'false', 'category' => 'features', 'description' => 'Allow Anonymous Telemetry'],
            ['key' => 'allow_marketplace', 'value' => 'true', 'category' => 'features', 'description' => 'Allow VS Code Marketplace (Extensions)'],
            ['key' => 'max_tokens_per_request', 'value' => '4096', 'category' => 'limits', 'description' => 'Max tokens per AI request'],
        ];

        foreach ($settings as $setting) {
            AiSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // ── Default AI Models ──
        $models = [
            // ═══ Autocomplete (Gemini 2.5 Flash — cheapest & fastest) ═══
            [
                'provider' => 'google',
                'model_id' => 'gemini-2.5-flash',
                'display_name' => 'Gemini 2.5 Flash',
                'role' => 'autocomplete',
                'is_default' => true,
                'is_active' => true,
                'context_length' => 1048576,
                'sort_order' => 1,
            ],

            // ═══ Chat Models ═══
            [
                'provider' => 'anthropic',
                'model_id' => 'claude-sonnet-4-20250514',
                'display_name' => 'Claude Sonnet 4',
                'role' => 'chat',
                'is_default' => true,
                'is_active' => true,
                'context_length' => 200000,
                'sort_order' => 1,
            ],
            [
                'provider' => 'google',
                'model_id' => 'gemini-2.5-pro',
                'display_name' => 'Gemini 2.5 Pro',
                'role' => 'chat',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 1048576,
                'sort_order' => 2,
            ],
            [
                'provider' => 'openai',
                'model_id' => 'gpt-4.1',
                'display_name' => 'GPT-4.1',
                'role' => 'chat',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 1047576,
                'sort_order' => 3,
            ],
            [
                'provider' => 'deepseek',
                'model_id' => 'deepseek-chat',
                'display_name' => 'DeepSeek V3',
                'role' => 'chat',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 128000,
                'sort_order' => 4,
            ],
            [
                'provider' => 'anthropic',
                'model_id' => 'claude-3-5-haiku-20241022',
                'display_name' => 'Claude 3.5 Haiku',
                'role' => 'chat',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 200000,
                'sort_order' => 5,
            ],
            [
                'provider' => 'openai',
                'model_id' => 'gpt-4.1-mini',
                'display_name' => 'GPT-4.1 Mini',
                'role' => 'chat',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 1047576,
                'sort_order' => 6,
            ],

            // ═══ Agent Models (most powerful) ═══
            [
                'provider' => 'anthropic',
                'model_id' => 'claude-opus-4-20250514',
                'display_name' => 'Claude Opus 4',
                'role' => 'agent',
                'is_default' => true,
                'is_active' => true,
                'context_length' => 200000,
                'sort_order' => 1,
            ],
            [
                'provider' => 'anthropic',
                'model_id' => 'claude-sonnet-4-20250514',
                'display_name' => 'Claude Sonnet 4',
                'role' => 'agent',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 200000,
                'sort_order' => 2,
            ],
            [
                'provider' => 'google',
                'model_id' => 'gemini-2.5-pro',
                'display_name' => 'Gemini 2.5 Pro',
                'role' => 'agent',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 1048576,
                'sort_order' => 3,
            ],

            // ═══ Edit Model (fast & cheap for inline edits) ═══
            [
                'provider' => 'openai',
                'model_id' => 'gpt-4.1-mini',
                'display_name' => 'GPT-4.1 Mini',
                'role' => 'edit',
                'is_default' => true,
                'is_active' => true,
                'context_length' => 1047576,
                'sort_order' => 1,
            ],
            [
                'provider' => 'google',
                'model_id' => 'gemini-2.5-flash',
                'display_name' => 'Gemini 2.5 Flash',
                'role' => 'edit',
                'is_default' => false,
                'is_active' => true,
                'context_length' => 1048576,
                'sort_order' => 2,
            ],
        ];

        foreach ($models as $model) {
            AiModel::firstOrCreate(
                [
                    'provider' => $model['provider'],
                    'model_id' => $model['model_id'],
                    'role' => $model['role'],
                ],
                $model
            );
        }
    }
}
