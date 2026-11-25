@props(['variant' => 'default', 'size' => 'sm', 'dot' => false])

@php
$variants = [
    'default' => 'text-vl-ink-secondary bg-vl-surface-raised',
    'success' => 'text-vl-teal bg-vl-teal/12',
    'warning' => 'text-vl-gate bg-vl-gate/14',
    'danger'  => 'text-vl-danger bg-vl-danger/14',
    'info'    => 'text-vl-accent bg-vl-accent/12',
    'brand'   => 'text-vl-accent bg-vl-accent/12',
];
$sizeClasses = $size === 'xs' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 {$sizeClasses} rounded-full font-medium " . ($variants[$variant] ?? $variants['default'])]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current shrink-0"></span>
    @endif
    {{ $slot }}
</span>
