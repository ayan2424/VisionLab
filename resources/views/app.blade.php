<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#05080F">
    <meta name="description" content="VisionLab delivers deterministic Nix-powered VS Code workspaces, auditable AI patch governance, and university LMS &amp; ERP infrastructure.">
    <title inertia>{{ config('app.name', 'VisionLab') }}</title>

    @include('partials.theme-init')

    {{-- Architectural Display (Clash Display) + UI Sans (Satoshi) + Code Mono (JetBrains Mono) --}}
    <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
    <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=clash-display@500,600,700&f[]=satoshi@400,500,700,900&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="min-h-screen bg-vl-bg text-vl-ink font-sans antialiased overflow-x-hidden selection:bg-sky-500/25 selection:text-sky-200">
    @inertia
</body>
</html>
