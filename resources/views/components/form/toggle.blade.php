@props(['label' => null, 'enabled' => false, 'name' => null])

<label class="flex items-center gap-3 cursor-pointer group">
    <div class="relative" x-data="{ on: @js($enabled) }">
        <input type="hidden" name="{{ $name }}" :value="on ? '1' : '0'">
        <button type="button" x-on:click="on = !on"
            :class="on ? 'bg-[var(--vc-accent)]' : 'bg-[var(--vc-border)]'"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--vc-accent)] focus:ring-offset-2 focus:ring-offset-[var(--vc-bg)]"
            role="switch" :aria-checked="on">
            <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 shadow-sm"></span>
        </button>
    </div>
    @if($label)
        <span class="text-sm font-medium text-[var(--vc-text)]">{{ $label }}</span>
    @endif
</label>


