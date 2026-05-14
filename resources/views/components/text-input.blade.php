@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-surface border border-border text-slate-200 placeholder-muted rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/20 transition-all disabled:opacity-50']) }}>
