<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-red-600/90 hover:bg-red-500 border border-red-500/40 rounded-xl font-bold text-sm text-white transition-all focus:outline-none focus:ring-2 focus:ring-red-500/40']) }}>
    {{ $slot }}
</button>
