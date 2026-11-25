@props(['label' => null, 'error' => null, 'help' => null, 'required' => false])

<div>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-vl-ink mb-1.5">
            {{ $label }}
            @if($required)<span class="text-vl-accent ml-0.5">*</span>@endif
        </label>
    @endif

    <input {{ $attributes->merge(['class' => 'vl-input' . ($error ? ' !border-vl-danger focus:!ring-vl-danger/20' : '')]) }}>

    @if($help && !$error)
        <p class="mt-1.5 text-xs text-vl-ink-muted">{{ $help }}</p>
    @endif

    @if($error)
        <p class="mt-1.5 text-xs text-vl-danger">{{ $error }}</p>
    @endif
</div>
