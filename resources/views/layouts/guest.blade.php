@props(['title' => 'Sign in'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090D14">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <title>{{ $title }} — VisionLab</title>

    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col vl-atmosphere text-vl-ink font-sans antialiased">
    {{-- Minimal Auth Folio Topbar --}}
    <header class="w-full px-5 sm:px-8 h-16 flex items-center justify-between max-w-6xl mx-auto">
        <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg py-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vl-accent">
            <x-logo size="h-7 w-7" textSize="text-[17px]" />
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-xs font-medium text-vl-ink-secondary hover:text-vl-ink transition-colors duration-150">
                Back to overview
            </a>
            <x-theme-toggle />
        </div>
    </header>

    {{-- Centered Sculptural Folio Card --}}
    <main class="flex-1 flex items-center justify-center px-5 py-10">
        <div class="w-full max-w-[440px]">
            <div class="vl-panel p-7 sm:p-9">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Subtle Institutional Footer --}}
    <footer class="w-full px-5 py-6 text-center text-xs text-vl-ink-muted">
        &copy; {{ date('Y') }} VisionLab &middot; Institutional Coding Infrastructure
    </footer>
</body>
</html>
