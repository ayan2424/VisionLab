@props(['striped' => false, 'hoverable' => true])

<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-xl border border-[var(--vc-border)]']) }}>
    <table class="w-full text-sm text-left">
        @isset($head)
            <thead class="bg-[var(--vc-elevated)]/50 border-b border-[var(--vc-border)]">
                <tr>{{ $head }}</tr>
            </thead>
        @endisset
        <tbody class="{{ $striped ? '[&>tr:nth-child(even)]:bg-[var(--vc-elevated)]/30' : '' }} {{ $hoverable ? '[&>tr]:hover:bg-[var(--vc-elevated)]/50' : '' }}">
            {{ $slot }}
        </tbody>
    </table>
</div>


