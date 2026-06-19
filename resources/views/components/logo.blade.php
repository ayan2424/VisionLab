@props(['size' => 'h-9 w-9', 'showText' => true, 'textSize' => 'text-lg'])

<div class="flex items-center gap-2.5 group">
    <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Logo" class="{{ $size }} object-contain transition-transform duration-300 group-hover:scale-105">
    @if($showText)
    <span class="{{ $textSize }} font-bold tracking-tight text-white">
        Vision<span class="text-gradient-violet">Lab</span>
    </span>
    @endif
</div>


