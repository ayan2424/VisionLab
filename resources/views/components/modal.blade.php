@props([
    'name' => 'modal',
    'maxWidth' => 'lg',
    'title' => '',
    'show' => false,
])

@php
$maxWidthClass = match($maxWidth) {
    'sm'  => 'max-w-sm',
    'md'  => 'max-w-md',
    'lg'  => 'max-w-lg',
    'xl'  => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    'full' => 'max-w-full',
};
@endphp

<div
    x-data="{ open: @js($show) }"
    x-show="open"
    x-on:open-modal-{{ $name }}.window="open = true"
    x-on:close-modal-{{ $name }}.window="open = false"
    x-on:keydown.escape.window="open = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-{{ $name }}-title"
>
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-on:click="open = false"></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full {{ $maxWidthClass }} bg-[var(--vc-surface)] border border-[var(--vc-border)] rounded-2xl shadow-2xl overflow-hidden"
    >
        @if($title)
        <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--vc-border)]">
            <h3 id="modal-{{ $name }}-title" class="text-lg font-semibold text-[var(--vc-text)]">{{ $title }}</h3>
            <button x-on:click="open = false" class="p-1 rounded-lg text-[var(--vc-muted)] hover:text-[var(--vc-text)] hover:bg-[var(--vc-elevated)] transition-colors" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        <div class="px-6 py-4">{{ $slot }}</div>

        @isset($footer)
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[var(--vc-border)] bg-[var(--vc-elevated)]/30">
            {{ $footer }}
        </div>
        @endisset
    </div>
</div>
