@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'icon' => null,
])

@php
$sizeClasses = match($size) {
    'xs' => 'px-3 py-1.5 text-xs rounded-lg',
    'sm' => 'px-3.5 py-2 text-xs rounded-xl',
    'md' => 'px-4 py-2.5 text-sm rounded-xl',
    'lg' => 'px-5 py-3 text-sm rounded-xl',
    'xl' => 'px-6 py-3.5 text-base rounded-xl',
    default => 'px-4 py-2.5 text-sm rounded-xl',
};

$variantClasses = match($variant) {
    'primary', 'glow' => 'vl-btn-primary',
    'secondary' => 'vl-btn-secondary',
    'ghost' => 'inline-flex items-center justify-center gap-2 font-medium text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised active:scale-[0.97] transition-[color,background-color,transform] duration-150 ease-vl-out cursor-pointer disabled:opacity-50 disabled:pointer-events-none',
    'danger' => 'vl-btn-danger',
    'success' => 'inline-flex items-center justify-center gap-2 font-semibold bg-vl-teal/15 text-vl-teal hover:bg-vl-teal/25 active:scale-[0.97] transition-[background-color,transform] duration-150 ease-vl-out cursor-pointer disabled:opacity-50 disabled:pointer-events-none',
    default => 'vl-btn-primary',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$variantClasses} {$sizeClasses}"]) }}>
        @if($icon)<span class="w-4 h-4 shrink-0">{!! $icon !!}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => "{$variantClasses} {$sizeClasses}"]) }}>
        @if($icon)<span class="w-4 h-4 shrink-0">{!! $icon !!}</span>@endif
        {{ $slot }}
    </button>
@endif
