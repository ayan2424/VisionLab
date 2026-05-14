<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F05000">
    <meta name="description" content="VisionLab — Collaborative coding platform for universities.">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/icons/icon-192.png">
    
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

    {{-- Theme script to prevent FOUC --}}
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
<body class="transition-colors duration-300 font-sans" style="background:var(--vc-bg); color:var(--vc-text);">

    <div class="flex h-screen overflow-hidden" x-data="{ mobileMenuOpen: false }">
        
        {{-- Sidebar (Hidden on mobile, toggled via x-show) --}}
        <x-sidebar />

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="mobileMenuOpen" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="mobileMenuOpen = false" x-transition.opacity></div>
        
        {{-- Mobile Sidebar Panel --}}
        <div x-show="mobileMenuOpen" class="fixed inset-y-0 left-0 z-50 w-64 shadow-xl md:hidden transform transition-transform duration-300" 
             x-transition:enter="translate-x-0" x-transition:leave="-translate-x-full" style="background:var(--vc-surface); border-color:var(--vc-border);">
             <div class="p-4 flex justify-end">
                 <button @click="mobileMenuOpen = false" class="p-2 text-vc-muted hover:text-vc-text transition-colors">
                     <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                 </button>
             </div>
             {{-- Mobile Sidebar Content (We can duplicate sidebar links here or restructure) --}}
             <div class="px-6 pb-6">
                 <a href="{{ route('dashboard') }}" class="block py-3 font-semibold text-vc-text">Dashboard</a>
                 <a href="{{ route('courses.index') }}" class="block py-3 font-semibold text-vc-text">Courses</a>
                 <a href="{{ route('workspace.index') }}" class="block py-3 font-semibold text-vc-text">Workspace</a>
                 @if(Auth::check() && Auth::user()->isAdmin())
                 <a href="{{ route('admin.dashboard') }}" class="block py-3 font-semibold text-red-500">Admin Panel</a>
                 @endif
                 <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t" style="border-color:var(--vc-border);">
                     @csrf
                     <button type="submit" class="w-full text-left py-3 font-semibold text-red-500">Sign Out</button>
                 </form>
             </div>
        </div>

        {{-- Main Content Wrapper --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden md:ml-64">
            {{-- Navbar --}}
            <x-navbar :title="$title ?? 'Dashboard'" />

            {{-- Main Scrollable Area --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8" id="main-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Global Toast Container --}}
    <div id="toast-container" class="fixed top-16 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
    <script>
    function vcToast(msg, type = 'info', duration = 5000) {
        const colors = {
            success: { bg:'rgba(22,163,74,.1)', border:'rgba(22,163,74,.3)', text:'var(--vc-success)', icon:'M5 13l4 4L19 7' },
            error:   { bg:'rgba(220,38,38,.1)', border:'rgba(220,38,38,.3)', text:'var(--vc-danger)',  icon:'M6 18L18 6M6 6l12 12' },
            warn:    { bg:'rgba(217,119,6,.1)', border:'rgba(217,119,6,.3)', text:'var(--vc-warning)', icon:'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
            info:    { bg:'rgba(37,99,235,.1)', border:'rgba(37,99,235,.3)', text:'var(--vc-info)', icon:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        };
        const c = colors[type] || colors.info;
        const el = document.createElement('div');
        el.style.cssText = `pointer-events:auto;display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:12px;border:1px solid ${c.border};background:${c.bg};backdrop-filter:blur(12px);box-shadow:var(--vc-shadow-md);min-width:260px;max-width:360px;animation:toastIn .25s ease;`;
        el.innerHTML = `<svg style="width:16px;height:16px;color:${c.text};flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${c.icon}"/></svg><div style="flex:1;"><div style="font-size:12px;font-weight:600;color:${c.text};margin-bottom:1px;">${String(msg).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div></div><button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--vc-muted);cursor:pointer;font-size:14px;padding:0;">&times;</button>`;
        document.getElementById('toast-container').appendChild(el);
        setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(),300); }, duration);
    }
    window.vcToast = vcToast;
    </script>
    <style>@keyframes toastIn { from { opacity:0; transform:translateX(16px); } to { opacity:1; transform:translateX(0); } }</style>
</body>
</html>
