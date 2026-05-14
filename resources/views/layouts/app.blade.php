<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F05000">
    <meta name="description" content="VisionLab — Collaborative coding platform for universities. Full VS Code IDE, AI Agent, and Smart LMS.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="VisionLab">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
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
    <div id="toast-container" class="fixed top-16 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    {{-- Global toast helper --}}
    <script>
    function vcToast(msg, type = 'info', duration = 5000) {
        const colors = {
            success: { bg:'rgba(5,150,105,.12)', border:'rgba(5,150,105,.3)',  text:'var(--vc-success)', icon:'M5 13l4 4L19 7' },
            error:   { bg:'rgba(220,38,38,.1)',  border:'rgba(220,38,38,.3)',  text:'var(--vc-danger)',  icon:'M6 18L18 6M6 6l12 12' },
            warn:    { bg:'rgba(217,119,6,.1)',   border:'rgba(217,119,6,.3)',  text:'var(--vc-warning)', icon:'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
            info:    { bg:'rgba(37,99,235,.1)',  border:'rgba(37,99,235,.3)', text:'var(--vc-info)',  icon:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        };
        const c = colors[type] || colors.info;
        const el = document.createElement('div');
        el.style.cssText = `pointer-events:auto;display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:12px;border:1px solid ${c.border};background:${c.bg};backdrop-filter:blur(12px);box-shadow:var(--vc-shadow-md);min-width:260px;max-width:360px;animation:toastIn .25s ease;`;
        el.innerHTML = `<svg style="width:16px;height:16px;color:${c.text};flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${c.icon}"/></svg><div style="flex:1;"><div style="font-size:12px;font-weight:600;color:${c.text};margin-bottom:1px;">${escHtml(msg)}</div></div><button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--vc-muted);cursor:pointer;font-size:14px;line-height:1;padding:0;flex-shrink:0;">&times;</button>`;
        document.getElementById('toast-container').appendChild(el);
        setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(),300); }, duration);
    }
    function escHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    window.vcToast = vcToast;
    </script>

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
                vcToast(
                    `"${data.assignment_title}" graded: ${data.grade}/${data.max_points} (${pct}%)`,
                    pct >= 80 ? 'success' : pct >= 60 ? 'warn' : 'error',
                    8000
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

    <style>
    @keyframes toastIn { from { opacity:0; transform:translateX(16px); } to { opacity:1; transform:translateX(0); } }
    </style>
</body>
</html>
