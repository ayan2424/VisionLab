<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#090D14">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <title>@yield('title', 'Error') — VisionLab</title>

    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col vl-atmosphere text-vl-ink font-sans antialiased">
    <header class="w-full max-w-5xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center">
            <x-logo size="h-7 w-7" textSize="text-[17px]" />
        </a>
        <x-theme-toggle />
    </header>

    <main class="flex-1 flex items-center justify-center px-5 py-12">
        <div class="vl-panel w-full max-w-lg p-8 sm:p-11 text-center space-y-5">
            <p class="font-mono text-6xl sm:text-7xl font-bold tracking-tight text-vl-accent tabular-nums">
                @yield('code', 'Error')
            </p>

            <div class="space-y-2">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-vl-ink">
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
