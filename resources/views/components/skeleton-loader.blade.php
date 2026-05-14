@props(['class' => '', 'width' => '100%', 'height' => '16px', 'rounded' => '8px', 'count' => 1])

@for($i = 0; $i < $count; $i++)
<div
    class="{{ $class }}"
    style="
        width: {{ $width }};
        height: {{ $height }};
        border-radius: {{ $rounded }};
        background: linear-gradient(90deg, #161b22 25%, #21262d 50%, #161b22 75%);
        background-size: 200% 100%;
        animation: shimmer 2.5s linear infinite;
        {{ $i > 0 ? 'margin-top: 8px;' : '' }}
    "
></div>
@endfor

@once
<style>
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
@endonce
