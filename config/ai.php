<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | The primary AI provider. Supported: "gemini", "anthropic"
    | Gemini uses Google's Gemini 2.0 Flash API.
    | Anthropic uses Claude Opus/Sonnet API.
    |
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models',
    ],

    /*
    |--------------------------------------------------------------------------
    | Anthropic (Claude) Configuration
    |--------------------------------------------------------------------------
    */
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY', ''),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
        'endpoint' => 'https://api.anthropic.com/v1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sandbox Configuration
    |--------------------------------------------------------------------------
    |
    | Directories and files the AI agent is allowed to read/write.
    | Everything else is OFF LIMITS.
    |
    */
    'sandbox' => [
        'allowed_paths' => [
            'app/',
            'resources/views/',
            'routes/',
            'public/',
            'config/',
            'database/',
            'tests/',
        ],
        'denied_paths' => [
            '.env',
            'vendor/',
            'storage/',
            'bootstrap/',
            'node_modules/',
        ],
        'denied_functions' => [
            'exec', 'shell_exec', 'system', 'passthru', 'popen',
            'proc_open', 'pcntl_exec', 'eval', 'assert',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory File
    |--------------------------------------------------------------------------
    |
    | The AI agent persists learned user preferences and project context
    | in this file within each workspace root.
    |
    */
    'memory_file' => '.VisionLab_memory.md',
];
