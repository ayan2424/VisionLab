{{--
  Dashboard Layout — Unified Layout System (Firebase × Amber Tech)
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F05000">
    <meta name="description" content="VisionLab — Collaborative coding platform for universities.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/icons/icon-192.png">
    <title>@yield('title', 'Dashboard') — VisionLab</title>

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

    {{-- Theme flash prevention --}}
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
<body class="font-sans antialiased overflow-hidden transition-colors duration-300" style="background:var(--vc-bg);color:var(--vc-text);">

<div class="flex h-screen">

    {{-- ═══ SIDEBAR ═══ --}}
    <x-sidebar />

    {{-- ═══ MAIN AREA ═══ --}}
    <div class="flex-1 flex flex-col overflow-hidden md:ml-64">

        {{-- ── Topbar ── --}}
        <x-topbar />

        {{-- ── Content ── --}}
        <main class="flex-1 overflow-y-auto p-6 transition-colors duration-300">
            @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2"
                 style="background:rgba(22,163,74,0.08);border:1px solid rgba(22,163,74,0.15);color:var(--vc-success);">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2"
                 style="background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.15);color:var(--vc-danger);">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') ?? $errors->first() }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-16 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
<script>
function vcToast(msg, type = 'info', duration = 5000) {
    const colors = {
        success: { bg:'rgba(22,163,74,.1)', border:'rgba(22,163,74,.3)', text:'var(--vc-success)', icon:'M5 13l4 4L19 7' },
        error:   { bg:'rgba(220,38,38,.1)', border:'rgba(220,38,38,.3)', text:'var(--vc-danger)',  icon:'M6 18L18 6M6 6l12 12' },
        warn:    { bg:'rgba(217,119,6,.1)', border:'rgba(217,119,6,.3)', text:'var(--vc-warning)', icon:'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
        info:    { bg:'rgba(37,99,235,.1)', border:'rgba(37,99,235,.3)', text:'var(--vc-info)',    icon:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const c = colors[type] || colors.info;
    const el = document.createElement('div');
    el.style.cssText = `pointer-events:auto;display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:12px;border:1px solid ${c.border};background:${c.bg};backdrop-filter:blur(12px);box-shadow:var(--vc-shadow-md);min-width:260px;max-width:360px;animation:toastIn .25s ease;`;
    el.innerHTML = `<svg style="width:16px;height:16px;color:${c.text};flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${c.icon}"/></svg><div style="flex:1;"><div style="font-size:12px;font-weight:600;color:${c.text};">${String(msg).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div></div><button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--vc-muted);cursor:pointer;font-size:14px;padding:0;">&times;</button>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(),300); }, duration);
}
window.vcToast = vcToast;
</script>

<style>
@keyframes fadeSlideUp {
    from { opacity:0;transform:translateY(14px); }
    to   { opacity:1;transform:translateY(0); }
}
@keyframes toastIn {
    from { opacity:0;transform:translateX(16px); }
    to   { opacity:1;transform:translateX(0); }
}
</style>
</body>
</html>
