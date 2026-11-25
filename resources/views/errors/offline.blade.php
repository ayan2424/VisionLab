<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#090D14">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <title>Offline — VisionLab</title>

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
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-vl-accent/14 text-vl-accent">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                </svg>
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl font-bold tracking-tight text-vl-ink">You are currently offline</h1>
                <p class="text-sm leading-relaxed text-vl-ink-secondary max-w-md mx-auto">
                    VisionLab requires an active network connection to synchronize cloud IDE containers, AI patch reviews, and assignment snapshots.
                </p>
            </div>

            <div class="pt-2">
                <button type="button" onclick="window.location.reload()" class="vl-btn-primary">
                    Retry Connection
                </button>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-vl-ink-muted">
        &copy; {{ date('Y') }} VisionLab
    </footer>
</body>
</html>
