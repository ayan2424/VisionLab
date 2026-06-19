@props(['variant' => 'info', 'dismissible' => true, 'title' => null])

@php
$variants = [
    'info'    => ['bg' => 'bg-blue-500/10 border-blue-500/20', 'icon' => '💡', 'text' => 'text-blue-400'],
    'success' => ['bg' => 'bg-emerald-500/10 border-emerald-500/20', 'icon' => '✓', 'text' => 'text-emerald-400'],
    'warning' => ['bg' => 'bg-cyan-500/10 border-cyan-500/20', 'icon' => '⚠', 'text' => 'text-cyan-400'],
    'danger'  => ['bg' => 'bg-red-500/10 border-red-500/20', 'icon' => '✕', 'text' => 'text-red-400'],
];
$v = $variants[$variant] ?? $variants['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition
     {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-xl border {$v['bg']}"]) }}
     role="alert">
    <span class="{{ $v['text'] }} text-lg mt-0.5">{{ $v['icon'] }}</span>
    <div class="flex-1 min-w-0">
        @if($title)
            <p class="font-semibold text-sm {{ $v['text'] }} mb-1">{{ $title }}</p>
        @endif
        <p class="text-sm text-[var(--vc-text-secondary)]">{{ $slot }}</p>
    </div>
    @if($dismissible)
        <button x-on:click="show = false" class="p-1 rounded text-[var(--vc-muted)] hover:text-[var(--vc-text)] transition-colors" aria-label="Dismiss">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>


