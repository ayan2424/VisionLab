<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0a0a">
    <meta name="description" content="VisionLab — Collaborative coding platform for universities. Full VS Code IDE, AI Agent, and Smart LMS.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="VisionLab">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <link rel="apple-touch-icon" href="/logo.svg">
    <script src="/pwa.js" defer></script>
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>{{ isset($title) ? $title . ' — VisionLab' : 'VisionLab' }}</title>

    @php
        $reverbKey    = env('REVERB_APP_KEY', '');
        $reverbHost   = env('REVERB_HOST', 'localhost');
        $reverbPort   = (int) env('REVERB_PORT', 8080);
        $reverbScheme = env('REVERB_SCHEME', 'http');
    @endphp

    <meta name="reverb-key"    content="{{ $reverbKey }}">
    <meta name="reverb-host"   content="{{ $reverbHost }}">
    <meta name="reverb-port"   content="{{ $reverbPort }}">
    <meta name="reverb-scheme" content="{{ $reverbScheme }}">

    @if(Auth::check())
    <meta name="auth-user-id"   content="{{ Auth::id() }}">
    <meta name="auth-user-role" content="{{ Auth::user()->role }}">
    @endif

    {{-- Prevent flash of wrong theme --}}
    <script>
        (function() {
            var t = localStorage.getItem('vc-theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen transition-colors duration-300" style="background:var(--vc-bg);color:var(--vc-text);">
    @if(Auth::check())
    @include('layouts.navigation')
    @endif

    {{ $slot }}

    {{-- Global toast container --}}
    <x-toast-container />

    {{-- PWA + Echo --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('/serviceworker.js').catch(()=>{}));
    }

    @if(Auth::check())
    (function initGlobalEcho() {
        const key    = document.querySelector('meta[name="reverb-key"]')?.content;
        const host   = document.querySelector('meta[name="reverb-host"]')?.content;
        const port   = parseInt(document.querySelector('meta[name="reverb-port"]')?.content || '8080');
        const scheme = document.querySelector('meta[name="reverb-scheme"]')?.content || 'http';
        if (!key) return;

        window.addEventListener('load', () => {
            if (typeof window.initEcho === 'function') {
                window.initEcho({ key, host, port, scheme });
                initUserNotifications();
            }
        });
    })();

    function initUserNotifications() {
        if (!window.Echo) return;
        const userId = document.querySelector('meta[name="auth-user-id"]')?.content;
        if (!userId) return;

        window.Echo.private(`user.${userId}`)
            .listen('.submission.graded', (data) => {
                const pct = data.max_points > 0 ? Math.round(data.grade / data.max_points * 100) : 0;
                window.VisionCode.toast(
                    `"${data.assignment_title}" graded: ${data.grade}/${data.max_points} (${pct}%)`,
                    pct >= 80 ? 'success' : pct >= 60 ? 'warning' : 'error'
                );
                const notifs = JSON.parse(localStorage.getItem('vc_notifs') || '[]');
                notifs.unshift({
                    id:    data.submission_id,
                    title: data.assignment_title,
                    grade: data.grade,
                    max:   data.max_points,
                    pct,
                    from:  data.grader_name,
                    at:    Date.now(),
                    read:  false,
                });
                localStorage.setItem('vc_notifs', JSON.stringify(notifs.slice(0, 20)));
                updateBellBadge();
            });
    }

    function updateBellBadge() {
        const notifs  = JSON.parse(localStorage.getItem('vc_notifs') || '[]');
        const unread  = notifs.filter(n => !n.read).length;
        const badge   = document.getElementById('notif-badge');
        const counter = document.getElementById('notif-count');
        if (!badge) return;
        badge.style.display = unread > 0 ? 'flex' : 'none';
        if (counter) counter.textContent = unread > 9 ? '9+' : unread;
    }

    document.addEventListener('DOMContentLoaded', updateBellBadge);
    window.updateBellBadge = updateBellBadge;
    @endif
    </script>

    <script src="{{ asset('build/assets/animations.js') }}"></script>
</body>
</html>


