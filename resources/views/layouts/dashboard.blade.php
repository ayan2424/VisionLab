{{-- VisionLab — Luminous Atelier Docked Operate Shell --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#090D14">
    <meta name="description" content="VisionLab — Sovereign university coding labs, LMS, and campus ERP.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/icons/logo.svg">
    <script src="/pwa.js" defer></script>
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>@yield('title', 'Dashboard') — VisionLab</title>

    @if(Auth::check())
        <meta name="auth-user-id" content="{{ Auth::id() }}">
        <meta name="auth-user-role" content="{{ Auth::user()->role }}">
    @endif

    @include('partials.theme-init')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @yield('styles')
</head>
<body
    x-data="{ sidebarOpen: false }"
    @keydown.escape.window="sidebarOpen = false"
    class="font-sans antialiased bg-vl-bg text-vl-ink overflow-hidden"
>
    <div class="flex h-screen w-full overflow-hidden">
        {{-- Mobile Sidebar Backdrop --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity.duration.150ms
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-[2px] md:hidden"
            aria-hidden="true"
        ></div>

        {{-- 240px Docked Sidebar Rail --}}
        <x-sidebar />

        {{-- Right Main Column --}}
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-vl-bg">
            {{-- 52px Seamless Topbar --}}
            <x-topbar />

            {{-- Primary Workspace Content Canvas --}}
            <main class="flex-1 min-h-0 overflow-y-auto">
                <div class="mx-auto max-w-[1360px] px-5 sm:px-7 py-6 space-y-5">
                    @if(session()->has('impersonator_id'))
                        <div class="rounded-2xl bg-vl-danger/12 px-4 py-3 text-sm text-vl-danger flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2 font-medium">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>You are currently impersonating <strong class="font-semibold">{{ Auth::user()->name }}</strong>.</span>
                            </div>
                            <a href="{{ route('stop_impersonating') }}" class="rounded-xl bg-vl-danger/20 hover:bg-vl-danger/30 px-3 py-1.5 text-xs font-semibold transition-colors duration-150">
                                Stop Impersonating
                            </a>
                        </div>
                    @endif

                    @if(session('success') || session('status'))
                        <x-alert type="success" dismissible>
                            {{ session('success') ?? session('status') }}
                        </x-alert>
                    @endif

                    @if(session('error') || $errors->any())
                        <x-alert type="danger" dismissible>
                            {{ session('error') ?? $errors->first() }}
                        </x-alert>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Global 0ms Instant Command Palette --}}
    @auth
        <x-command-palette />
    @endauth

    {{-- Reusable Confirmation Dialog --}}
    <div id="vc-confirm-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="vc-confirm-title">
        <div id="vc-confirm-backdrop" onclick="vcConfirmClose()" class="absolute inset-0 bg-black/55 backdrop-blur-[2px] opacity-0 transition-opacity duration-150 ease-vl-out"></div>
        <div id="vc-confirm-box" class="relative w-full max-w-sm rounded-2xl bg-vl-surface p-6 shadow-vl-overlay transform scale-95 opacity-0 transition-[transform,opacity] duration-150 ease-vl-out">
            <h3 id="vc-confirm-title" class="text-base font-bold text-vl-ink">Confirm Action</h3>
            <p id="vc-confirm-message" class="mt-2 text-sm leading-relaxed text-vl-ink-secondary"></p>
            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" onclick="vcConfirmClose()" class="vl-btn-secondary !py-2 !px-4 !text-xs">Cancel</button>
                <button type="button" id="vc-confirm-btn" class="vl-btn-primary !py-2 !px-4 !text-xs">Confirm</button>
            </div>
        </div>
    </div>

    {{-- Toast Notification Stack --}}
    <div id="toast-container" class="fixed top-16 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" aria-live="polite"></div>

    <script>
        let vcConfirmCallback = null;

        function vcConfirm(msg, callback) {
            const modal = document.getElementById('vc-confirm-modal');
            const backdrop = document.getElementById('vc-confirm-backdrop');
            const box = document.getElementById('vc-confirm-box');
            document.getElementById('vc-confirm-message').innerText = msg;
            vcConfirmCallback = callback;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;

            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }

        function vcConfirmClose() {
            const modal = document.getElementById('vc-confirm-modal');
            const backdrop = document.getElementById('vc-confirm-backdrop');
            const box = document.getElementById('vc-confirm-box');

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                vcConfirmCallback = null;
            }, 160);
        }

        document.getElementById('vc-confirm-btn')?.addEventListener('click', () => {
            if (vcConfirmCallback) vcConfirmCallback();
            vcConfirmClose();
        });
        window.vcConfirm = vcConfirm;

        function vcToast(msg, type = 'info', duration = 4500) {
            const palettes = {
                success: { tone: 'rgb(var(--vl-teal))', bg: 'rgb(var(--vl-surface))', icon: 'M5 13l4 4L19 7' },
                error:   { tone: 'rgb(var(--vl-danger))', bg: 'rgb(var(--vl-surface))', icon: 'M6 18L18 6M6 6l12 12' },
                warn:    { tone: 'rgb(var(--vl-gate))', bg: 'rgb(var(--vl-surface))', icon: 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
                info:    { tone: 'rgb(var(--vl-accent))', bg: 'rgb(var(--vl-surface))', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
            };
            const p = palettes[type] || palettes.info;
            const el = document.createElement('div');
            el.className = 'pointer-events-auto flex items-start gap-3 rounded-xl px-4 py-3 shadow-vl-overlay min-w-[260px] max-w-[360px]';
            el.style.background = p.bg;
            const safeMsg = String(msg).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            el.innerHTML = `<svg class="h-4 w-4 shrink-0 mt-0.5" style="color:${p.tone}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="${p.icon}"/></svg><div class="flex-1 text-xs font-medium text-vl-ink leading-relaxed">${safeMsg}</div><button type="button" onclick="this.parentElement.remove()" class="text-vl-ink-muted hover:text-vl-ink text-sm leading-none">&times;</button>`;
            document.getElementById('toast-container')?.appendChild(el);
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 180ms cubic-bezier(0.22, 1, 0.36, 1)';
                setTimeout(() => el.remove(), 190);
            }, duration);
        }
        window.vcToast = vcToast;
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
