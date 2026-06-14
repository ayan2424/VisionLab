@props(['label' => null, 'error' => null, 'help' => null, 'required' => false, 'maxlength' => null])

<div>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-[var(--vc-text)] mb-1.5">
            {{ $label }}
            @if($required)<span class="text-[var(--vc-accent)]">*</span>@endif
        </label>
    @endif

    <div class="relative">
        <textarea {{ $attributes->merge(['class' => 'vc-input min-h-[100px] resize-y' . ($error ? ' !border-red-500' : ''), 'maxlength' => $maxlength]) }}>{{ $slot }}</textarea>
        @if($maxlength)
            <span class="absolute bottom-2 right-3 text-xs text-[var(--vc-muted)]" x-data x-text="$el.previousElementSibling.value.length + '/{{ $maxlength }}'"></span>
        @endif
    </div>

    @if($help && !$error)
        <p class="mt-1.5 text-xs text-[var(--vc-muted)]">{{ $help }}</p>
    @endif
    @if($error)
        <p class="mt-1.5 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
