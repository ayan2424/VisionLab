@props(['label' => null, 'enabled' => false, 'name' => null])

<label class="flex items-center gap-3 cursor-pointer group">
    <div class="relative" x-data="{ on: @js($enabled) }">
        <input type="hidden" name="{{ $name }}" :value="on ? '1' : '0'">
        <button
            type="button"
            x-on:click="on = !on"
            :class="on ? 'bg-vl-accent-cta' : 'bg-vl-surface-raised'"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 ease-vl-out focus-visible:outline-none cursor-pointer"
            role="switch"
            :aria-checked="on"
        >
            <span
                :class="on ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 ease-vl-out shadow-sm"
            ></span>
        </button>
    </div>
    @if($label)
        <span class="text-sm font-medium text-vl-ink">{{ $label }}</span>
    @endif
</label>
