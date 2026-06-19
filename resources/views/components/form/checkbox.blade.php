@props(['label' => null, 'checked' => false, 'description' => null])

<label class="flex items-start gap-3 cursor-pointer group">
    <input type="checkbox" {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'mt-1 w-4 h-4 rounded border-[var(--vc-border)] bg-[var(--vc-input)] text-[var(--vc-accent)] focus:ring-[var(--vc-accent)] focus:ring-offset-0 cursor-pointer']) }}>
    <div>
        @if($label)
            <span class="text-sm font-medium text-[var(--vc-text)] group-hover:text-[var(--vc-accent)] transition-colors">{{ $label }}</span>
        @endif
        @if($description)
            <p class="text-xs text-[var(--vc-muted)] mt-0.5">{{ $description }}</p>
        @endif
    </div>
</label>


