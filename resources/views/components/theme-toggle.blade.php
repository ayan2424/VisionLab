@props(['size' => 'md'])

@php
$dims = $size === 'sm' ? 'h-8 w-8 rounded-lg' : 'h-9 w-9 rounded-xl';
$iconDims = $size === 'sm' ? 'h-4 w-4' : 'h-[18px] w-[18px]';
@endphp

<button
    type="button"
    x-data
    @click="$store.theme.toggle()"
    :aria-label="$store.theme.isDark ? 'Switch to Pearl Light mode' : 'Switch to Obsidian Dark mode'"
    :title="$store.theme.isDark ? 'Switch to Light Mode (Lecture Hall)' : 'Switch to Dark Mode (Night Lab)'"
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center {$dims} text-vl-ink-secondary hover:text-vl-ink hover:bg-vl-surface-raised active:scale-[0.97] transition-[color,background-color,transform] duration-150 ease-vl-out cursor-pointer focus-visible:outline-none"
    ]) }}
>
    {{-- Sun icon (shown when in Dark mode to switch to Light) --}}
    <svg x-show="$store.theme.isDark" x-cloak class="{{ $iconDims }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
    </svg>

    {{-- Moon icon (shown when in Light mode to switch to Dark) --}}
    <svg x-show="!$store.theme.isDark" class="{{ $iconDims }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
    </svg>
</button>
