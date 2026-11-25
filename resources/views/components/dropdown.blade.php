@props(['align' => 'right', 'width' => '48', 'trigger' => null])

@php
$alignClasses = match($align) {
    'left'   => 'left-0 origin-top-left',
    'right'  => 'right-0 origin-top-right',
    'center' => 'left-1/2 -translate-x-1/2 origin-top',
    default  => 'right-0 origin-top-right',
};
$widthClass = match((string) $width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    default => "w-{$width}",
};
@endphp

<div x-data="{ open: false }" class="relative inline-block" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
    <div x-on:click="open = !open" class="cursor-pointer">
        @if($trigger)
            {{ $trigger }}
        @else
            <x-button variant="ghost" size="sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
            </x-button>
        @endif
    </div>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-vl-out duration-150"
        x-transition:enter-start="opacity-0 scale-[0.96] -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-vl-out duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-[0.96] -translate-y-1"
        class="absolute z-50 mt-2 {{ $alignClasses }} {{ $widthClass }} bg-vl-surface rounded-xl shadow-vl-overlay overflow-hidden p-1"
    >
        {{ $slot }}
    </div>
</div>
