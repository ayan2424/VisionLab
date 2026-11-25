@extends('layouts.admin')
@section('title', 'System Status')
@section('page-title', 'System Status')

@section('content')
@php
    // Exclude decommissioned jitsi_config from UI operational rollup while keeping /healthz JSON untouched
    $activeChecks = collect($checks ?? [])->except('jitsi_config');
    $allOperational = $activeChecks->isNotEmpty() && $activeChecks->every(fn ($v) => $v === true);
    $criticalDown = !($activeChecks['database'] ?? false) || !($activeChecks['redis'] ?? false);

    $overallLabel = match (true) {
        $allOperational => 'All Systems Operational',
        $criticalDown => 'Critical Outage Detected',
        default => 'Partial Degradation',
    };

    $overallVariant = match (true) {
        $allOperational => 'success',
        $criticalDown => 'danger',
        default => 'warning',
    };

    $services = [
        'database'  => ['label' => 'Primary Database', 'description' => 'SQL connection pool and query responsiveness'],
        'redis'     => ['label' => 'Redis Cache & Queues', 'description' => 'Session store, job queue workers, and rate-limiter bus'],
        'reverb'    => ['label' => 'Realtime Broadcast (Reverb)', 'description' => 'WebSocket broadcast driver configuration for IDE patch notifications'],
        'storage'   => ['label' => 'Local Artifact Storage', 'description' => 'Disk write verification for uploads, VSIX packages, and snapshots'],
        'ai_config' => ['label' => 'AI Provider Configuration', 'description' => 'Configured LLM provider endpoint and API credentials'],
    ];
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl font-bold tracking-tight text-vl-ink">System Status</h2>
            <p class="text-sm text-vl-ink-secondary">
                Live infrastructure probe &middot; {{ now()->format('D, d M Y · H:i:s') }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <x-badge :variant="$overallVariant" dot class="!px-3 !py-1.5 !text-xs">
                {{ $overallLabel }}
            </x-badge>
            <a href="{{ route('health') }}" target="_blank" rel="noopener" class="vl-btn-secondary !py-1.5 !px-3.5 !text-xs">
                Raw /healthz JSON
            </a>
        </div>
    </div>

    {{-- Summary Strip --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card
            label="Active Services Healthy"
            :value="$activeChecks->filter()->count() . ' / ' . $activeChecks->count()"
            :tone="$allOperational ? 'success' : ($criticalDown ? 'danger' : 'warning')"
        />
        <x-stat-card
            label="Operational State"
            :value="$overallLabel"
            :tone="$overallVariant"
        />
        <x-stat-card
            label="Last Probe Timestamp"
            :value="now()->format('H:i:s')"
            subtitle="Auto-refreshes every 60 seconds"
            tone="neutral"
        />
    </div>

    {{-- Service Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($services as $key => $service)
            @php $up = (bool) ($activeChecks[$key] ?? false); @endphp
            <div class="vl-panel p-5 space-y-3 {{ $up ? '' : 'ring-1 ring-vl-danger/30' }}">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-sm font-bold text-vl-ink">{{ $service['label'] }}</h3>
                    <x-badge :variant="$up ? 'success' : 'danger'" dot>
                        {{ $up ? 'Operational' : 'Unavailable' }}
                    </x-badge>
                </div>
                <p class="text-xs leading-relaxed text-vl-ink-secondary">{{ $service['description'] }}</p>
            </div>
        @endforeach
    </div>

    @if(!$allOperational)
        <x-alert type="{{ $criticalDown ? 'danger' : 'warning' }}" title="Infrastructure Attention Needed">
            One or more active VisionLab dependencies reported unavailable. Verify container processes, queue workers, and `.env` provider credentials.
        </x-alert>
    @endif

    <p class="text-xs text-center text-vl-ink-muted">
        Decommissioned legacy modules are excluded from this rollup. Machine-readable endpoint:
        <a href="{{ route('health') }}" target="_blank" rel="noopener" class="font-mono text-vl-accent hover:underline">/healthz</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
    setTimeout(() => window.location.reload(), 60000);
</script>
@endpush
