@extends('layouts.admin')
@section('title', 'System Status')
@section('page-title', 'System Status')

@section('content')
@php
    $allOperational = collect($checks)->every(fn($v) => $v === true);
    $criticalDown   = !$checks['database'] || !$checks['redis'];

    $overallLabel = match(true) {
        $allOperational => 'All Systems Operational',
        $criticalDown   => 'Critical Outage Detected',
        default         => 'Partial Degradation',
    };

    $overallColor = match(true) {
        $allOperational => '#10b981',  // emerald-500
        $criticalDown   => '#ef4444',  // red-500
        default         => '#f59e0b',  // amber-500
    };

    $services = [
        'database'     => ['label' => 'Database',          'icon' => 'database',   'description' => 'MySQL primary node connectivity and query availability'],
        'redis'        => ['label' => 'Cache & Queues',    'icon' => 'server',     'description' => 'Redis session store, job queue, and rate-limiter bus'],
        'reverb'       => ['label' => 'Real-Time Engine',  'icon' => 'zap',        'description' => 'Laravel Reverb WebSocket broadcast driver'],
        'storage'      => ['label' => 'File Storage',      'icon' => 'hard-drive', 'description' => 'Local disk write access for uploads and artifacts'],
        'ai_config'    => ['label' => 'AI Agent',          'icon' => 'cpu',        'description' => 'Anthropic Claude API credentials and provider configuration'],
        'jitsi_config' => ['label' => 'Video Sessions',    'icon' => 'video',      'description' => 'Jitsi Meet application ID and JWT configuration'],
    ];
@endphp

{{-- ── Page Header ──────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8"
     style="opacity:0;animation:fadeSlideUp .5s .05s ease forwards">
    <div>
        <h1 class="text-2xl font-bold" style="color:var(--vc-text);">System Status</h1>
        <p class="text-sm mt-1" style="color:var(--vc-text-secondary);">
            Infrastructure probe — {{ now()->format('D, d M Y · H:i:s') }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Live status badge --}}
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold"
             style="background:{{ $allOperational ? 'rgba(16,185,129,0.1)' : ($criticalDown ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)') }};
                    color:{{ $overallColor }};
                    border:1px solid {{ $allOperational ? 'rgba(16,185,129,0.25)' : ($criticalDown ? 'rgba(239,68,68,0.25)' : 'rgba(245,158,11,0.25)') }};">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-60"
                      style="background:{{ $overallColor }};"></span>
                <span class="relative inline-flex rounded-full h-2 w-2"
                      style="background:{{ $overallColor }};"></span>
            </span>
            {{ $overallLabel }}
        </div>
        <a href="/healthz" target="_blank"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
           style="background:var(--vc-surface-2);color:var(--vc-text-secondary);border:1px solid var(--vc-border);">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            JSON API
        </a>
    </div>
</div>

{{-- ── Summary Bar ──────────────────────────────────────────────── --}}
<div class="mb-6 rounded-xl p-5"
     style="opacity:0;animation:fadeSlideUp .5s .1s ease forwards;
            background:var(--vc-surface);border:1px solid var(--vc-border);">
    <div class="flex flex-wrap gap-6">
        <div>
            <p class="text-xs uppercase tracking-widest font-semibold mb-1" style="color:var(--vc-muted);">Services Healthy</p>
            <p class="text-3xl font-bold" style="color:var(--vc-text);">
                {{ collect($checks)->filter()->count() }}<span class="text-lg" style="color:var(--vc-muted);">/{{ count($checks) }}</span>
            </p>
        </div>
        <div class="w-px self-stretch" style="background:var(--vc-border);"></div>
        <div>
            <p class="text-xs uppercase tracking-widest font-semibold mb-1" style="color:var(--vc-muted);">Overall Status</p>
            <p class="text-lg font-semibold" style="color:{{ $overallColor }};">{{ $overallLabel }}</p>
        </div>
        <div class="w-px self-stretch" style="background:var(--vc-border);"></div>
        <div>
            <p class="text-xs uppercase tracking-widest font-semibold mb-1" style="color:var(--vc-muted);">Checked At</p>
            <p class="text-sm font-mono" style="color:var(--vc-text-secondary);">{{ now()->format('H:i:s') }}</p>
        </div>
    </div>
</div>

{{-- ── Service Cards Grid ───────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach($services as $key => $service)
        @php
            $up          = $checks[$key];
            $statusColor = $up ? '#10b981' : '#ef4444';
            $statusLabel = $up ? 'Operational' : 'Unavailable';
            $statusBg    = $up ? 'rgba(16,185,129,0.08)' : 'rgba(239,68,68,0.08)';
            $statusBdr   = $up ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.25)';
            $delay       = ['database'=>'.12s','redis'=>'.17s','reverb'=>'.22s','storage'=>'.27s','ai_config'=>'.32s','jitsi_config'=>'.37s'][$key];
        @endphp
        <div class="rounded-xl p-5 transition-all duration-200"
             style="opacity:0;animation:fadeSlideUp .5s {{ $delay }} ease forwards;
                    background:var(--vc-surface);
                    border:1px solid {{ $up ? 'var(--vc-border)' : 'rgba(239,68,68,0.3)' }};">

            <div class="flex items-start justify-between gap-3 mb-3">
                {{-- Icon --}}
                <div class="flex h-9 w-9 items-center justify-center rounded-lg flex-shrink-0"
                     style="background:var(--vc-surface-2);border:1px solid var(--vc-border);">
                    @switch($service['icon'])
                        @case('database')
                            <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <ellipse cx="12" cy="5" rx="9" ry="3" stroke-width="2"/>
                                <path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5" stroke-width="2" stroke-linecap="round"/>
                                <path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('server')
                            <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect x="2" y="2" width="20" height="8" rx="2" stroke-width="2"/>
                                <rect x="2" y="14" width="20" height="8" rx="2" stroke-width="2"/>
                                <line x1="6" y1="6" x2="6.01" y2="6" stroke-width="3" stroke-linecap="round"/>
                                <line x1="6" y1="18" x2="6.01" y2="18" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('zap')
                            <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('hard-drive')
                            <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M22 12H2" stroke-width="2"/>
                                <path d="M5.45 5.11L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.45-6.89A2 2 0 0016.76 4H7.24a2 2 0 00-1.79 1.11z" stroke-width="2" stroke-linecap="round"/>
                                <line x1="6" y1="16" x2="6.01" y2="16" stroke-width="3" stroke-linecap="round"/>
                                <line x1="10" y1="16" x2="10.01" y2="16" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('cpu')
                            <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2"/>
                                <rect x="9" y="9" width="6" height="6" stroke-width="2"/>
                                <path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('video')
                            <svg class="w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect x="2" y="6" width="14" height="12" rx="2" stroke-width="2"/>
                                <path d="M16 10l5-3v10l-5-3V10z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                    @endswitch
                </div>

                {{-- Status badge --}}
                <div class="flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider flex-shrink-0"
                     style="background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusBdr }};">
                    <span class="h-1.5 w-1.5 rounded-full flex-shrink-0" style="background:{{ $statusColor }};"></span>
                    {{ $statusLabel }}
                </div>
            </div>

            <p class="text-sm font-semibold mb-0.5" style="color:var(--vc-text);">{{ $service['label'] }}</p>
            <p class="text-xs leading-relaxed" style="color:var(--vc-muted);">{{ $service['description'] }}</p>
        </div>
    @endforeach
</div>

{{-- ── Incident Banner (only when something is down) ───────────── --}}
@if(!$allOperational)
<div class="rounded-xl p-5 mb-6"
     style="opacity:0;animation:fadeSlideUp .5s .4s ease forwards;
            background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.2);">
    <div class="flex items-start gap-3">
        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold mb-0.5" style="color:#f59e0b;">Active Incident Detected</p>
            <p class="text-xs leading-relaxed" style="color:var(--vc-text-secondary);">
                One or more infrastructure services are unavailable. Check your Docker containers, environment
                variables, and server logs. The platform may have degraded functionality until all services are restored.
            </p>
        </div>
    </div>
</div>
@endif

{{-- ── Footer Note ──────────────────────────────────────────────── --}}
<p class="text-xs text-center" style="color:var(--vc-muted);opacity:0;animation:fadeSlideUp .5s .45s ease forwards;">
    This page auto-refreshes every 60 seconds &nbsp;·&nbsp;
    Machine-readable endpoint: <a href="/healthz" target="_blank" class="underline hover:opacity-80">/healthz</a>
</p>

@endsection

@section('scripts')
<script>
    // Auto-refresh every 60s to reflect live infrastructure state
    setTimeout(() => location.reload(), 60_000);
</script>
@endsection
