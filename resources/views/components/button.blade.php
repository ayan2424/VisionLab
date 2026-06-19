@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'icon' => null,
])

@php
$baseClasses = 'relative inline-flex items-center justify-center gap-2 font-semibold cursor-pointer transition-all duration-200 ease-out rounded-xl disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = match($size) {
    'xs' => 'px-3 py-1.5 text-xs',
    'sm' => 'px-4 py-2 text-xs',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-sm',
    'xl' => 'px-8 py-3.5 text-base',
};

$variantClasses = match($variant) {
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'ghost' => 'btn-ghost',
    'glow' => 'btn-glow',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 hover:shadow-lg border border-transparent',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 hover:shadow-lg border border-transparent',
    default => 'btn-primary',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}>
        @if($icon)<span class="w-4 h-4">{!! $icon !!}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}>
        @if($icon)<span class="w-4 h-4">{!! $icon !!}</span>@endif
        {{ $slot }}
    </button>
@endif


