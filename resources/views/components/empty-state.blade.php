@props(['icon' => null, 'title' => 'Nothing here yet', 'description' => null, 'actionText' => null, 'actionUrl' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-6 text-center']) }}>
    @if($icon)
        <div class="w-16 h-16 rounded-2xl bg-[var(--vc-elevated)] flex items-center justify-center mb-4">
            <span class="text-3xl">{{ $icon }}</span>
        </div>
    @else
        <div class="w-16 h-16 rounded-2xl bg-[var(--vc-elevated)] flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-[var(--vc-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        </div>
    @endif
    <h3 class="text-lg font-semibold text-[var(--vc-text)] mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-[var(--vc-muted)] max-w-sm mb-4">{{ $description }}</p>
    @endif
    @if($actionText && $actionUrl)
        <x-button variant="primary" size="sm" :href="$actionUrl">{{ $actionText }}</x-button>
    @endif
    {{ $slot }}
</div>
