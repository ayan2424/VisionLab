<nav id="mainNav" class="fixed inset-x-0 top-0 z-50 transition-all duration-500 border-b border-black/5 dark:border-white/5 bg-background/50 backdrop-blur-2xl">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-cyan/30 to-transparent"></div>
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6">
        <a href="{{ route('home') }}" class="group flex items-center gap-3 text-decoration-none text-foreground transition-transform hover:scale-[1.02]">
            <!-- Official Logo SVG -->
            <div class="relative flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-cyan/20 blur-xl transition-opacity group-hover:bg-cyan/40"></div>
                <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Logo" class="relative h-9 w-9 object-contain drop-shadow-[0_0_15px_rgba(23,195,214,0.5)]">
            </div>
            <div class="flex flex-col">
                <span class="font-display text-lg font-semibold tracking-tight">Vision<span class="text-gradient-violet">Lab</span></span>
                <span class="hidden font-mono text-[9px] uppercase tracking-[0.3em] text-cyan-light sm:block opacity-80">Research Kernel</span>
            </div>
        </a>
        <div class="hidden items-center gap-1 rounded-full border border-black/10 dark:border-white/10 bg-black/[0.03] dark:bg-white/[0.03] p-1.5 md:flex shadow-[0_0_30px_-10px_rgba(0,0,0,0.1)] dark:shadow-[0_0_30px_-10px_rgba(0,0,0,0.5)]">
            <a href="{{ route('about') }}" class="rounded-full px-5 py-2 font-mono text-[11px] uppercase tracking-[0.2em] text-muted-foreground transition-all hover:bg-black/5 dark:hover:bg-white/10 hover:text-foreground text-decoration-none">
                About
            </a>
            <a href="{{ route('features') }}" class="rounded-full px-5 py-2 font-mono text-[11px] uppercase tracking-[0.2em] text-muted-foreground transition-all hover:bg-black/5 dark:hover:bg-white/10 hover:text-foreground text-decoration-none">
                Features
            </a>
            <a href="{{ route('pricing') }}" class="rounded-full px-5 py-2 font-mono text-[11px] uppercase tracking-[0.2em] text-muted-foreground transition-all hover:bg-black/5 dark:hover:bg-white/10 hover:text-foreground text-decoration-none">
                Pricing
            </a>
            <a href="{{ route('docs') }}" class="rounded-full px-5 py-2 font-mono text-[11px] uppercase tracking-[0.2em] text-muted-foreground transition-all hover:bg-black/5 dark:hover:bg-white/10 hover:text-foreground text-decoration-none">
                Docs
            </a>
        </div>
        <div class="flex items-center gap-4">
            @auth
            <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-cyan px-6 py-2.5 font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-background transition-all hover:scale-105 hover:shadow-[0_0_40px_-10px_rgba(23,195,214,0.8)] text-decoration-none">
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent translate-x-[-100%] transition-transform duration-700 group-hover:translate-x-[100%]"></span>
                Dashboard
            </a>
            @else
            <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-cyan px-6 py-2.5 font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-background transition-all hover:scale-105 hover:shadow-[0_0_40px_-10px_rgba(23,195,214,0.8)] text-decoration-none">
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent translate-x-[-100%] transition-transform duration-700 group-hover:translate-x-[100%]"></span>
                Deploy Instance
            </a>
            @endauth
        </div>
    </div>
</nav>
