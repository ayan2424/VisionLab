<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Container Configuration
    |--------------------------------------------------------------------------
    */
    'container_prefix' => env('VISIONLAB_CONTAINER_PREFIX', 'vl-ws-'),
    'container_image'  => env('VISIONLAB_CONTAINER_IMAGE', 'visionlab/workspace:latest'),
    'storage_path'     => env('VISIONLAB_STORAGE_PATH', storage_path('workspaces')),
    'base_port'        => (int) env('VISIONLAB_BASE_PORT', 9000),
    'proxy_host'       => env('VISIONLAB_PROXY_HOST', '127.0.0.1'),

    /*
    |--------------------------------------------------------------------------
    | Resource Quotas (Defaults)
    |--------------------------------------------------------------------------
    */
    'default_memory_mb'       => (int) env('VISIONLAB_DEFAULT_MEMORY', 512),
    'default_cpu_shares'      => (int) env('VISIONLAB_DEFAULT_CPU_SHARES', 1024),
    'default_disk_mb'         => (int) env('VISIONLAB_DEFAULT_DISK', 1024),
    'default_timeout_minutes' => (int) env('VISIONLAB_DEFAULT_TIMEOUT', 120),

    /*
    |--------------------------------------------------------------------------
    | AI Agent Configuration
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'provider'    => env('VISIONLAB_AI_PROVIDER', 'anthropic'),
        'model'       => env('VISIONLAB_AI_MODEL', 'claude-3-5-sonnet-20241022'),
        'api_key'     => env('VISIONLAB_AI_API_KEY', ''),
        'max_tokens'  => (int) env('VISIONLAB_AI_MAX_TOKENS', 4096),
        'temperature' => (float) env('VISIONLAB_AI_TEMPERATURE', 0.3),
        'daily_limit' => (int) env('VISIONLAB_AI_DAILY_LIMIT', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Video (Jitsi) Configuration
    |--------------------------------------------------------------------------
    */
    'jitsi' => [
        'domain'     => env('VISIONLAB_JITSI_DOMAIN', 'meet.jit.si'),
        'app_id'     => env('VISIONLAB_JITSI_APP_ID', ''),
        'jwt_secret' => env('VISIONLAB_JITSI_JWT_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Deployment Providers
    |--------------------------------------------------------------------------
    */
    'deploy' => [
        'vercel_token'   => env('VISIONLAB_VERCEL_TOKEN', ''),
        'railway_token'  => env('VISIONLAB_RAILWAY_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | PWA / Push Notifications
    |--------------------------------------------------------------------------
    */
    'vapid' => [
        'public_key'  => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
        'subject'     => env('VAPID_SUBJECT', 'mailto:admin@visionlab.dev'),
    ],
];
