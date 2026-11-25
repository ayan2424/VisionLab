@props(['label' => '', 'value' => '', 'change' => null, 'changeLabel' => '', 'icon' => null, 'color' => 'brand'])

@php
$colorClasses = match($color) {
    'brand', 'info' => 'text-vl-accent bg-vl-accent/12',
    'success' => 'text-vl-teal bg-vl-teal/12',
    'warning' => 'text-vl-gate bg-vl-gate/12',
    'danger' => 'text-vl-danger bg-vl-danger/12',
    default => 'text-vl-accent bg-vl-accent/12',
};
@endphp

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    <div class="flex items-start justify-between gap-3 mb-3">
        <p class="text-xs font-medium text-vl-ink-secondary">{{ $label }}</p>
        @if($icon)
            <span class="w-8 h-8 rounded-xl inline-flex items-center justify-center shrink-0 {{ $colorClasses }}">
                {!! $icon !!}
            </span>
        @endif
    </div>

    <p class="text-2xl font-bold tracking-tight text-vl-ink font-mono tabular-nums mb-1">{{ $value }}</p>

    @if($change !== null)
        <div class="flex items-center gap-1.5 text-xs tabular-nums">
            @if($change >= 0)
                <svg class="w-3.5 h-3.5 text-vl-teal shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                </svg>
                <span class="font-medium text-vl-teal">+{{ $change }}%</span>
            @else
                <svg class="w-3.5 h-3.5 text-vl-danger shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
                <span class="font-medium text-vl-danger">{{ $change }}%</span>
            @endif
            @if($changeLabel)
                <span class="text-vl-ink-muted">{{ $changeLabel }}</span>
            @endif
        </div>
    @endif
</div>
