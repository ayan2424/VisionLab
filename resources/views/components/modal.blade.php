@props([
    'name' => 'modal',
    'maxWidth' => 'lg',
    'title' => '',
    'show' => false,
])

@php
$maxWidthClass = match($maxWidth) {
    'sm'   => 'max-w-sm',
    'md'   => 'max-w-md',
    'lg'   => 'max-w-lg',
    'xl'   => 'max-w-xl',
    '2xl'  => 'max-w-2xl',
    'full' => 'max-w-full',
    default => 'max-w-lg',
};
@endphp

<div
    x-data="{ open: @js($show) }"
    x-on:open-modal-{{ $name }}.window="open = true"
    x-on:close-modal-{{ $name }}.window="open = false"
    x-on:keydown.escape.window="open = false"
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-vl-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-vl-out duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-{{ $name }}-title"
        >
            <div class="absolute inset-0 bg-[var(--vc-overlay)] backdrop-blur-sm" x-on:click="open = false"></div>

            <div
                x-show="open"
                x-transition:enter="transition ease-vl-out duration-200"
                x-transition:enter-start="opacity-0 scale-[0.96] translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-vl-out duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-[0.96] translate-y-2"
                class="relative w-full {{ $maxWidthClass }} bg-vl-surface rounded-2xl shadow-vl-overlay overflow-hidden"
            >
                @if($title)
                    <div class="flex items-center justify-between px-6 py-4 border-b vl-divider">
                        <h3 id="modal-{{ $name }}-title" class="text-base font-semibold text-vl-ink">{{ $title }}</h3>
                        <button type="button" x-on:click="open = false" class="p-1.5 rounded-lg text-vl-ink-muted hover:text-vl-ink hover:bg-vl-surface-raised active:scale-[0.97] transition-[color,background-color,transform] duration-150 cursor-pointer" aria-label="Close">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <div class="px-6 py-5 text-vl-ink">{{ $slot }}</div>

                @isset($footer)
                    <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t vl-divider bg-vl-surface-raised/40">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </template>
</div>
