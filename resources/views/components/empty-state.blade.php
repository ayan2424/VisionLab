@props(['title', 'description', 'icon'])

<div class="flex flex-col items-center justify-center p-12 text-center border border-dashed rounded-xl border-white/10 bg-white/[0.02]">
    <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-white/5 text-slate-400">
        @if(isset($icon))
            {!! $icon !!}
        @else
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
        @endif
    </div>
    <h3 class="text-lg font-medium text-slate-200">{{ $title }}</h3>
    @if(isset($description))
        <p class="max-w-sm mt-2 text-sm text-slate-400">{{ $description }}</p>
    @endif
    @if(isset($action))
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
