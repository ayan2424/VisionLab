<footer class="border-t border-border bg-black/40 py-12">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-6 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Logo" class="h-6 w-6 object-contain">
            <span class="font-display text-sm font-semibold">VisionLab</span>
            <span class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground">© {{ date('Y') }} · research corp.</span>
        </div>
        <div class="flex flex-wrap gap-x-8 gap-y-2 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground">
            <a href="{{ route('about') }}" class="hover:text-foreground text-decoration-none text-muted-foreground">About</a>
            <a href="{{ route('features') }}" class="hover:text-foreground text-decoration-none text-muted-foreground">Features</a>
            <a href="{{ route('docs') }}" class="hover:text-foreground text-decoration-none text-muted-foreground">Docs</a>
            <a href="{{ route('contact') }}" class="hover:text-foreground text-decoration-none text-muted-foreground">Contact</a>
        </div>
    </div>
</footer>
