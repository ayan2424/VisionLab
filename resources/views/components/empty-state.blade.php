@props(['icon' => null, 'title' => 'Nothing here yet', 'description' => null, 'actionText' => null, 'actionUrl' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-6 text-center']) }}>
    <div class="w-12 h-12 rounded-2xl bg-vl-surface-raised text-vl-ink-muted flex items-center justify-center mb-4">
        @if($icon)
            <x-badge-icon :icon="$icon" class="w-6 h-6" />
        @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/>
            </svg>
        @endif
    </div>
    <h3 class="text-base font-semibold text-vl-ink mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-vl-ink-secondary max-w-sm mb-5">{{ $description }}</p>
    @endif
    @if($actionText && $actionUrl)
        <x-button variant="primary" size="sm" :href="$actionUrl">{{ $actionText }}</x-button>
    @endif
    {{ $slot }}
</div>
