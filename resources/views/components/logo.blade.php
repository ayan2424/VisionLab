@props(['size' => 'h-10 w-10', 'showText' => true, 'textSize' => 'text-xl', 'variant' => 'vibrant'])

@php
    $logoSrc = $variant === 'orange' ? 'icons/logo-orange.svg' : 'icons/logo.svg';
    $gradientClass = $variant === 'orange' ? 'text-gradient-orange' : 'text-gradient-violet';
@endphp

<div class="flex items-center gap-2.5 group">
    <img src="{{ asset($logoSrc) }}" alt="VisionLab Logo" class="{{ $size }} object-contain transition-transform duration-300 group-hover:scale-105">
    @if($showText)
    <span class="{{ $textSize }} font-bold tracking-tight text-white">
        Vision<span class="{{ $gradientClass }}">Lab</span>
    </span>
    @endif
</div>


