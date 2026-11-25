@props(['size' => 'h-9 w-9', 'showText' => true, 'textSize' => 'text-lg', 'variant' => 'default'])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 group select-none']) }}>
    {{-- Preserved 6-path VL >_ geometric mark adapted semantically to Light & Dark modes --}}
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 144 144"
        class="{{ $size }} shrink-0 text-vl-ink transition-transform duration-200 ease-vl-out group-hover:scale-[1.04]"
        aria-hidden="true"
    >
        <path fill="currentColor" d="M 11.3,33.3 L 54.6,110.5 L 63.4,110.5 L 78.9,83.2 L 63.2,83.2 L 59.1,89.9 L 27.3,33.3 Z"/>
        <path fill="currentColor" opacity="0.72" d="M 44.2,48.9 L 51.7,62.5 L 61.3,47.5 L 118.4,47.5 L 126.4,33.3 L 54.7,33.3 Z"/>
        <path fill="rgb(var(--vl-accent))" d="M 62.8,57.4 L 60.5,59.6 L 67.2,66.1 L 60.6,73.3 L 62.9,75.5 L 71.9,66.1 Z"/>
        <path fill="currentColor" opacity="0.88" d="M 105.7,55.7 L 89.8,55.7 L 97.0,68.7 L 73.5,110.7 L 89.5,110.7 L 113.3,68.9 Z"/>
        <path fill="currentColor" opacity="0.72" d="M 90.1,110.6 L 126.3,110.6 L 134.5,96.2 L 98.1,96.2 Z"/>
        <path fill="rgb(var(--vl-teal))" d="M 72.3,72.5 L 85.8,72.5 L 85.8,75.6 L 72.3,75.6 Z"/>
    </svg>

    @if($showText)
        <span class="{{ $textSize }} font-display font-bold tracking-[-0.025em] text-vl-ink">
            Vision<span class="text-vl-ink-secondary font-medium">Lab</span>
        </span>
    @endif
</div>
