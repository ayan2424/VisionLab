@props(['striped' => false, 'hoverable' => true])

<div {{ $attributes->merge(['class' => 'vl-panel overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
            @isset($head)
                <thead class="bg-vl-surface-raised/60 border-b vl-divider text-xs font-semibold text-vl-ink-secondary">
                    <tr>{{ $head }}</tr>
                </thead>
            @endisset
            <tbody class="divide-y divide-vl-border/5 {{ $striped ? '[&>tr:nth-child(even)]:bg-vl-surface-raised/35' : '' }} {{ $hoverable ? '[&>tr]:transition-colors [&>tr]:duration-150 [&>tr]:hover:bg-vl-surface-raised/60' : '' }}">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
