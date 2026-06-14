@props(['name' => '', 'initials' => '', 'src' => null, 'size' => 'md', 'color' => null])

@php
$sizeClasses = match($size) {
    'xs' => 'w-6 h-6 text-[10px]',
    'sm' => 'w-8 h-8 text-xs',
    'md' => 'w-10 h-10 text-sm',
    'lg' => 'w-12 h-12 text-base',
    'xl' => 'w-16 h-16 text-lg',
};
$bgColor = $color ?? 'var(--vc-accent)';
$displayInitials = $initials ?: strtoupper(substr($name, 0, 2));
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center rounded-full font-semibold text-white shrink-0 $sizeClasses"]) }}
     style="background-color: {{ $bgColor }};"
     title="{{ $name }}">
    @if($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="w-full h-full rounded-full object-cover">
    @else
        {{ $displayInitials }}
    @endif
</div>
