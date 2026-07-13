{{--
  Dashboard Layout — Unified Layout System (Firebase × cyan Tech)
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0a0a">
    <meta name="description" content="VisionLab — Collaborative coding platform for universities.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/logo.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/icons/logo.svg">
    <script src="/pwa.js" defer></script>
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>@yield('title', 'Dashboard') — VisionLab</title>

    @php

    @endphp


    @if(Auth::check())
    <meta name="auth-user-id"   content="{{ Auth::id() }}">
    <meta name="auth-user-role" content="{{ Auth::user()->role }}">
    @endif

    {{-- Theme flash prevention --}}
    <script>
        (function() {
            
        })();
    </script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased overflow-hidden transition-colors duration-300" style="background:var(--vc-bg);color:var(--vc-text);">

<div class="flex h-screen w-full p-3 md:p-5 gap-3 md:gap-5 box-border">

    {{-- ═══ SIDEBAR ═══ --}}
    <x-sidebar />

    {{-- ═══ MAIN AREA ═══ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden rounded-[1.5rem] md:rounded-[2rem] shadow-2xl relative transition-all duration-300" style="background:var(--vc-surface); border:1px solid var(--vc-border);">

        {{-- ── Topbar ── --}}
        <x-topbar />

        {{-- ── Content ── --}}
        <main class="flex-1 min-h-0 overflow-y-auto p-6 md:p-10 transition-colors duration-300 relative z-10 custom-scrollbar">
            @if(session()->has('impersonator_id'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center justify-between gap-4"
                 style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#EF4444;">
                <div class="flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    You are currently impersonating <strong>{{ Auth::user()->name }}</strong>.
                </div>
                <a href="{{ route('stop_impersonating') }}" class="px-3 py-1.5 rounded-lg font-bold text-xs bg-red-500/20 hover:bg-red-500/30 transition-colors">Stop Impersonating</a>
            </div>
            @endif
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

{{-- Custom Confirm Modal Container --}}
<div id="vc-confirm-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center pointer-events-none">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-auto" id="vc-confirm-backdrop" onclick="vcConfirmClose()"></div>
    
    {{-- Modal Box --}}
    <div class="relative w-full max-w-sm mx-4 p-6 rounded-2xl transform scale-95 opacity-0 transition-all duration-300 pointer-events-auto" 
         style="background:var(--vc-surface);border:1px solid var(--vc-border);box-shadow:var(--vc-shadow-xl);" 
         id="vc-confirm-box">
        <h3 class="text-lg font-bold mb-2" style="color:var(--vc-text);">Confirm Action</h3>
        <p class="text-sm mb-6" style="color:var(--vc-text-secondary);" id="vc-confirm-message"></p>
        <div class="flex items-center justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors hover:bg-black/5 dark:hover:bg-white/5" 
                    style="color:var(--vc-text-secondary);background:var(--vc-bg);border:1px solid var(--vc-border);" 
                    onclick="vcConfirmClose()">Cancel</button>
            <button type="button" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition-colors hover:brightness-110" 
                    style="background:var(--vc-accent);" 
                    id="vc-confirm-btn">Confirm</button>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-16 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
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
    
    // Trigger reflow
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
    }, 300);
}

document.getElementById('vc-confirm-btn')?.addEventListener('click', () => {
    if (vcConfirmCallback) {
        vcConfirmCallback();
    }
    vcConfirmClose();
});
window.vcConfirm = vcConfirm;

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
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.2);
}
</style>
</body>
</html>


