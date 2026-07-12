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
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/icons/logo.svg">
    <script src="/pwa.js" defer></script>
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>{{ isset($title) ? $title . ' — VisionLab' : 'VisionLab' }}</title>

    @php

    @endphp



    @if(Auth::check())
    <meta name="auth-user-id"   content="{{ Auth::id() }}">
    <meta name="auth-user-role" content="{{ Auth::user()->role }}">
    @endif

    {{-- Force Dark Mode Temporarily --}}
    

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen transition-colors duration-300" style="background:var(--vc-bg);color:var(--vc-text);">
    @if(Auth::check())
    @include('layouts.navigation')
    @endif

    {{ $slot }}

    {{-- Global toast container --}}
    <x-toast-container />

    {{-- PWA UI Elements --}}
    <div id="pwa-install-prompt" class="hidden fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50">
        <div>
            <div class="font-bold text-sm">Install VisionLab</div>
            <div class="text-xs text-blue-100">Add to your home screen for quick access.</div>
        </div>
        <button id="pwa-install-btn" class="bg-white text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-50 transition-colors">Install</button>
        <button id="pwa-install-close" class="text-blue-200 hover:text-white"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>

    {{-- PWA + Echo --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').then(reg => {
                console.log('SW Registered', reg);
                window.swRegistration = reg;
            }).catch(err => console.error('SW Error', err));
        });
    }

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        document.getElementById('pwa-install-prompt').classList.remove('hidden');
    });

    document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                document.getElementById('pwa-install-prompt').classList.add('hidden');
            }
            deferredPrompt = null;
        }
    });

    document.getElementById('pwa-install-close')?.addEventListener('click', () => {
        document.getElementById('pwa-install-prompt').classList.add('hidden');
    });

    // WebPush Subscription
    window.subscribeToPush = async function() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
        try {
            const reg = await navigator.serviceWorker.ready;
            const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
            if(!vapidPublicKey) return;

            const subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            });

            await fetch('/api/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('vl_token') // or sanctum cookie
                },
                body: JSON.stringify(subscription)
            });
            console.log('Subscribed to push notifications!');
        } catch(e) {
            console.error('Failed to subscribe to push', e);
        }
    };

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }


    @if(Auth::check())

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


