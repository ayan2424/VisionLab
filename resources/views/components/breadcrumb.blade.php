@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm']) }} aria-label="Breadcrumb">
    @foreach($items as $i => $item)
        @if($i > 0)
            <svg class="w-4 h-4 text-[var(--vc-muted)] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        @endif

        @if(isset($item['url']) && $i < count($items) - 1)
            <a href="{{ $item['url'] }}" class="text-[var(--vc-text-secondary)] hover:text-[var(--vc-text)] transition-colors">
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-[var(--vc-text)] font-medium truncate max-w-[200px]">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
