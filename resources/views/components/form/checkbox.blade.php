@props(['label' => null, 'checked' => false, 'description' => null])

<label class="flex items-start gap-3 cursor-pointer group">
    <input type="checkbox" @checked($checked)
        {{ $attributes->merge(['class' => 'mt-1 w-4 h-4 rounded border-vl-border-strong/25 bg-vl-surface text-vl-accent-cta focus:ring-vl-accent/40 focus:ring-offset-0 cursor-pointer']) }}>
    <div>
        @if($label)
            <span class="text-sm font-medium text-vl-ink group-hover:text-vl-accent transition-colors duration-150">{{ $label }}</span>
        @endif
        @if($description)
            <p class="text-xs text-vl-ink-muted mt-0.5">{{ $description }}</p>
        @endif
    </div>
</label>
