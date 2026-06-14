@props(['label' => null, 'error' => null, 'help' => null, 'required' => false])

<div>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-[var(--vc-text)] mb-1.5">
            {{ $label }}
            @if($required)<span class="text-[var(--vc-accent)]">*</span>@endif
        </label>
    @endif

    <input {{ $attributes->merge(['class' => 'vc-input' . ($error ? ' !border-red-500 !ring-red-500/20' : '')]) }}>

    @if($help && !$error)
        <p class="mt-1.5 text-xs text-[var(--vc-muted)]">{{ $help }}</p>
    @endif

    @if($error)
        <p class="mt-1.5 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
