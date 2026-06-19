@props(['type' => 'spinner', 'size' => 'md'])

@php
$sizeClasses = match($size) {
    'sm' => 'w-4 h-4',
    'md' => 'w-8 h-8',
    'lg' => 'w-12 h-12',
};
@endphp

@if($type === 'spinner')
    <div {{ $attributes->merge(['class' => "inline-flex items-center justify-center"]) }}>
        <svg class="{{ $sizeClasses }} animate-spin text-[var(--vc-accent)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
@elseif($type === 'skeleton')
    <div {{ $attributes->merge(['class' => 'skeleton h-4 w-full rounded-lg']) }}></div>
@elseif($type === 'dots')
    <div class="flex items-center gap-1">
        <span class="w-2 h-2 rounded-full bg-[var(--vc-accent)] animate-bounce" style="animation-delay: 0s;"></span>
        <span class="w-2 h-2 rounded-full bg-[var(--vc-accent)] animate-bounce" style="animation-delay: 0.15s;"></span>
        <span class="w-2 h-2 rounded-full bg-[var(--vc-accent)] animate-bounce" style="animation-delay: 0.3s;"></span>
    </div>
@endif


