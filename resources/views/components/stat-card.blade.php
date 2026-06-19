@props(['label' => '', 'value' => '', 'change' => null, 'changeLabel' => '', 'icon' => null, 'color' => 'brand'])

@php
$colorClasses = match($color) {
    'brand'   => 'text-[var(--vc-accent)]',
    'success' => 'text-emerald-400',
    'warning' => 'text-cyan-400',
    'danger'  => 'text-red-400',
    'info'    => 'text-blue-400',
    default   => 'text-[var(--vc-accent)]',
};
@endphp

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    <div class="flex items-start justify-between mb-3">
        <p class="text-sm font-medium text-[var(--vc-text-secondary)]">{{ $label }}</p>
        @if($icon)
            <span class="{{ $colorClasses }} opacity-60">
                {!! $icon !!}
            </span>
        @endif
    </div>

    <p class="text-2xl font-bold text-[var(--vc-text)] mb-1">{{ $value }}</p>

    @if($change !== null)
        <div class="flex items-center gap-1.5">
            @if($change >= 0)
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                <span class="text-xs font-medium text-emerald-400">+{{ $change }}%</span>
            @else
                <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <span class="text-xs font-medium text-red-400">{{ $change }}%</span>
            @endif
            @if($changeLabel)
                <span class="text-xs text-[var(--vc-muted)]">{{ $changeLabel }}</span>
            @endif
        </div>
    @endif
</div>


