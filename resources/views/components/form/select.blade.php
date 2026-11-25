@props(['label' => null, 'error' => null, 'required' => false, 'placeholder' => 'Select...', 'options' => []])

<div>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-vl-ink mb-1.5">
            {{ $label }}
            @if($required)<span class="text-vl-accent ml-0.5">*</span>@endif
        </label>
    @endif

    <select {{ $attributes->merge(['class' => 'vl-input appearance-none cursor-pointer' . ($error ? ' !border-vl-danger' : '')]) }}>
        @if($placeholder)
            <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
        {{ $slot }}
    </select>

    @if($error)
        <p class="mt-1.5 text-xs text-vl-danger">{{ $error }}</p>
    @endif
</div>
