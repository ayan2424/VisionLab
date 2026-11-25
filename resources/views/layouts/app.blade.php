<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090D14">
    <meta name="description" content="VisionLab — Sovereign university coding labs, AI patch governance, and campus LMS.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="VisionLab">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/icons/logo.svg">
    <script src="/pwa.js" defer></script>
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>{{ isset($title) ? $title . ' — VisionLab' : 'VisionLab' }}</title>

    @if(Auth::check())
        <meta name="auth-user-id" content="{{ Auth::id() }}">
        <meta name="auth-user-role" content="{{ Auth::user()->role }}">
    @endif

    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-vl-bg text-vl-ink">
    @if(Auth::check())
        @include('layouts.navigation')
    @endif

    {{ $slot }}

    <x-toast-container />

    {{-- PWA Install Prompt --}}
    <div id="pwa-install-prompt" class="hidden fixed bottom-4 right-4 vl-panel px-4 py-3 flex items-center gap-3 z-50">
        <div>
            <div class="font-semibold text-sm text-vl-ink">Install VisionLab</div>
            <div class="text-xs text-vl-ink-secondary">Add VisionLab for faster desktop launch.</div>
        </div>
        <button id="pwa-install-btn" class="vl-btn-primary !py-1.5 !px-3 !text-xs">Install</button>
        <button id="pwa-install-close" class="p-1 text-vl-ink-muted hover:text-vl-ink" aria-label="Dismiss install prompt">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    window.swRegistration = reg;
                }).catch(() => {});
            });
        }

        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('pwa-install-prompt')?.classList.remove('hidden');
        });

        document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                document.getElementById('pwa-install-prompt')?.classList.add('hidden');
            }
            deferredPrompt = null;
        });

        document.getElementById('pwa-install-close')?.addEventListener('click', () => {
            document.getElementById('pwa-install-prompt')?.classList.add('hidden');
        });
    </script>
</body>
</html>
