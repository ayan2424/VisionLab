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

    {{-- Executive Display (Plus Jakarta Sans) + Precision UI Sans (Inter) + Code Mono (JetBrains Mono) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="min-h-screen bg-vl-bg text-vl-ink font-sans antialiased overflow-x-hidden selection:bg-sky-500/25 selection:text-sky-200">
    @inertia
</body>
</html>
