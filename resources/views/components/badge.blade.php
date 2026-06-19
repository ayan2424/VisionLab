@props(['variant' => 'default', 'size' => 'sm', 'dot' => false])

@php
$variants = [
    'default'  => 'text-[var(--vc-text-secondary)] bg-[var(--vc-elevated)] border-[var(--vc-border)]',
    'success'  => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
    'warning'  => 'text-cyan-400 bg-cyan-400/10 border-cyan-400/20',
    'danger'   => 'text-red-400 bg-red-400/10 border-red-400/20',
    'info'     => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
    'brand'    => 'text-[var(--vc-accent)] bg-[var(--vc-accent)]/10 border-[var(--vc-accent)]/20',
];
$sizeClasses = $size === 'xs' ? 'px-2 py-0.5 text-[10px]' : 'px-3 py-1 text-xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 $sizeClasses rounded-full font-medium border " . ($variants[$variant] ?? $variants['default'])]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    @endif
    {{ $slot }}
</span>


