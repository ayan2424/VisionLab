@props([
    'padding' => 'p-6',
    'hover' => true,
])

<div {{ $attributes->merge(['class' => "vc-card $padding" . ($hover ? '' : ' hover:shadow-[var(--vc-shadow-sm)] hover:border-[var(--vc-border)]')]) }}>
    @isset($header)
        <div class="mb-4 pb-4 border-b border-[var(--vc-border)]">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-[var(--vc-border)]">
            {{ $footer }}
        </div>
    @endisset
</div>
