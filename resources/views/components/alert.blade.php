@props(['variant' => 'info', 'dismissible' => true, 'title' => null])

@php
$variants = [
    'info'    => ['wrap' => 'bg-vl-accent/10 text-vl-accent', 'title' => 'text-vl-accent'],
    'success' => ['wrap' => 'bg-vl-teal/12 text-vl-teal',     'title' => 'text-vl-teal'],
    'warning' => ['wrap' => 'bg-vl-gate/12 text-vl-gate',     'title' => 'text-vl-gate'],
    'danger'  => ['wrap' => 'bg-vl-danger/12 text-vl-danger', 'title' => 'text-vl-danger'],
];
$v = $variants[$variant] ?? $variants['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-vl-out duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-[0.98]"
    {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-xl {$v['wrap']}"]) }}
    role="alert"
>
    <span class="mt-0.5 shrink-0" aria-hidden="true">
        @switch($variant)
            @case('success')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                @break
            @case('warning')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                @break
            @case('danger')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                @break
            @default
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
        @endswitch
    </span>

    <div class="flex-1 min-w-0">
        @if($title)
            <p class="font-semibold text-sm {{ $v['title'] }} mb-0.5">{{ $title }}</p>
        @endif
        <div class="text-sm text-vl-ink-secondary">{{ $slot }}</div>
    </div>

    @if($dismissible)
        <button type="button" x-on:click="show = false" class="p-1 rounded-lg text-vl-ink-muted hover:text-vl-ink transition-colors cursor-pointer" aria-label="Dismiss">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
