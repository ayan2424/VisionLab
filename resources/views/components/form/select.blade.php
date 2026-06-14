@props(['label' => null, 'error' => null, 'required' => false, 'placeholder' => 'Select...', 'options' => []])

<div>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-[var(--vc-text)] mb-1.5">
            {{ $label }}
            @if($required)<span class="text-[var(--vc-accent)]">*</span>@endif
        </label>
    @endif

    <select {{ $attributes->merge(['class' => 'vc-input appearance-none cursor-pointer' . ($error ? ' !border-red-500' : '')]) }}>
        @if($placeholder)
            <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
        {{ $slot }}
    </select>

    @if($error)
        <p class="mt-1.5 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
