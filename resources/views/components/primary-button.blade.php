<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-violet-600 hover:bg-violet-500 border border-violet-500/40 rounded-xl font-bold text-sm text-white transition-all focus:outline-none focus:ring-2 focus:ring-violet-500/40']) }} style="box-shadow:0 0 12px rgba(124,58,237,0.35)">
    {{ $slot }}
</button>


