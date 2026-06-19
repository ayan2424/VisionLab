<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-transparent border border-border rounded-xl font-bold text-sm text-slate-400 hover:text-white hover:bg-white/[0.05] transition-all focus:outline-none focus:ring-2 focus:ring-slate-500/30 disabled:opacity-25']) }}>
    {{ $slot }}
</button>


