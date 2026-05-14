<div {{ $attributes->merge(['class' => 'animate-pulse bg-white/5 border border-white/10 rounded-lg overflow-hidden']) }}>
    @if(isset($type) && $type === 'card')
        <div class="h-40 bg-white/5 w-full"></div>
        <div class="p-4 space-y-3">
            <div class="h-4 bg-white/10 rounded w-3/4"></div>
            <div class="h-3 bg-white/10 rounded w-1/2"></div>
            <div class="h-3 bg-white/10 rounded w-5/6"></div>
        </div>
    @elseif(isset($type) && $type === 'text')
        <div class="h-4 bg-white/10 rounded w-full"></div>
    @elseif(isset($type) && $type === 'avatar')
        <div class="h-10 w-10 bg-white/10 rounded-full"></div>
    @else
        <!-- Generic block -->
        <div class="h-full w-full bg-white/10"></div>
    @endif
</div>
