@props(['type' => null])

<div {{ $attributes->merge(['class' => 'vl-panel overflow-hidden']) }}>
    @if($type === 'card')
        <div class="h-36 skeleton rounded-none w-full"></div>
        <div class="p-4 space-y-2.5">
            <div class="h-4 skeleton w-3/4"></div>
            <div class="h-3 skeleton w-1/2"></div>
            <div class="h-3 skeleton w-5/6"></div>
        </div>
    @elseif($type === 'text')
        <div class="h-4 skeleton w-full"></div>
    @elseif($type === 'avatar')
        <div class="h-10 w-10 skeleton rounded-full"></div>
    @else
        <div class="h-full min-h-[2rem] w-full skeleton"></div>
    @endif
</div>
