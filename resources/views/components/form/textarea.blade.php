@props(['label' => null, 'error' => null, 'help' => null, 'required' => false, 'maxlength' => null])

<div>
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block text-sm font-medium text-vl-ink mb-1.5">
            {{ $label }}
            @if($required)<span class="text-vl-accent ml-0.5">*</span>@endif
        </label>
    @endif

    <div class="relative">
        <textarea {{ $attributes->merge(['class' => 'vl-input min-h-[100px] resize-y' . ($error ? ' !border-vl-danger' : ''), 'maxlength' => $maxlength]) }}>{{ $slot }}</textarea>
        @if($maxlength)
            <span class="absolute bottom-2.5 right-3 text-xs font-mono tabular-nums text-vl-ink-muted" x-data x-text="$el.previousElementSibling.value.length + '/{{ $maxlength }}'"></span>
        @endif
    </div>

    @if($help && !$error)
        <p class="mt-1.5 text-xs text-vl-ink-muted">{{ $help }}</p>
    @endif
    @if($error)
        <p class="mt-1.5 text-xs text-vl-danger">{{ $error }}</p>
    @endif
</div>
