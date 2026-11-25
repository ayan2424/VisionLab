<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090D14">
    <meta name="description" content="@yield('meta_description', 'VisionLab delivers deterministic Nix-powered VS Code workspaces, auditable AI patch governance, and university LMS & ERP infrastructure.')">
    <title>@yield('title', 'VisionLab — Sovereign University Coding Labs')</title>

    @include('partials.theme-init')

    @stack('head-early')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col bg-vl-bg text-vl-ink font-sans antialiased overflow-x-hidden">
    <x-frontend-header />

    <main id="main-content" class="flex-1 pt-[60px]">
        @yield('content')
    </main>

    <x-frontend-footer />

    @stack('scripts')
    @yield('scripts')
</body>
</html>
