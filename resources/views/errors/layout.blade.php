<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#05080F">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <title>@yield('title', 'Error') — VisionLab</title>

    @include('partials.theme-init')

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex flex-col bg-vl-bg text-vl-ink font-sans antialiased">
    <header class="w-full max-w-5xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 select-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144 144" class="h-8 w-8 shrink-0 text-vl-ink" aria-hidden="true">
                <path fill="currentColor" d="M 11.3,33.3 L 54.6,110.5 L 63.4,110.5 L 78.9,83.2 L 63.2,83.2 L 59.1,89.9 L 27.3,33.3 Z"/>
                <path fill="currentColor" opacity="0.72" d="M 44.2,48.9 L 51.7,62.5 L 61.3,47.5 L 118.4,47.5 L 126.4,33.3 L 54.7,33.3 Z"/>
                <path fill="#0284C7" d="M 62.8,57.4 L 60.5,59.6 L 67.2,66.1 L 60.6,73.3 L 62.9,75.5 L 71.9,66.1 Z"/>
                <path fill="currentColor" opacity="0.88" d="M 105.7,55.7 L 89.8,55.7 L 97.0,68.7 L 73.5,110.7 L 89.5,110.7 L 113.3,68.9 Z"/>
                <path fill="currentColor" opacity="0.72" d="M 90.1,110.6 L 126.3,110.6 L 134.5,96.2 L 98.1,96.2 Z"/>
                <path fill="#0D9488" d="M 72.3,72.5 L 85.8,72.5 L 85.8,75.6 L 72.3,75.6 Z"/>
            </svg>
            <span class="font-display text-[17px] font-bold tracking-[-0.025em] text-vl-ink">
                Vision<span class="text-sky-500 font-medium">Lab</span>
            </span>
        </a>
    </header>

    <main class="flex-1 flex items-center justify-center px-5 py-12">
        <div class="vl-panel w-full max-w-lg p-8 sm:p-10 text-center space-y-5">
            <p class="font-mono text-6xl sm:text-7xl font-bold tracking-tight text-sky-500 tabular-nums">
                @yield('code', 'Error')
            </p>

            <div class="space-y-2">
                <h1 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-vl-ink">
                    @yield('message')
                </h1>
                <p class="text-sm leading-relaxed text-vl-ink-secondary max-w-md mx-auto">
                    @yield('description')
                </p>
            </div>

            <div class="pt-3 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/') }}" class="vl-btn-primary">
                    Return to VisionLab
                </a>
                <button type="button" onclick="window.history.back()" class="vl-btn-secondary">
                    Go Back
                </button>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-vl-ink-muted">
        &copy; {{ date('Y') }} VisionLab
    </footer>
</body>
</html>
