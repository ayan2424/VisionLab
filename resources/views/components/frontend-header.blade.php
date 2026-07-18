<nav id="mainNav" class="fixed inset-x-0 top-0 z-50 transition-all duration-500 bg-[#040d21]/70 backdrop-blur-2xl border-b border-white/[0.08] shadow-[0_4px_30px_rgba(0,0,0,0.1)]">
    <!-- Top thin gradient line -->
    <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-[#10B981]/50 to-transparent opacity-70"></div>

    <div class="mx-auto flex h-16 max-w-[1400px] items-center justify-between px-6 lg:px-8">
        <!-- Logo Section -->
        <a href="{{ route('home') }}" class="group flex items-center gap-3 transition-transform hover:scale-105 duration-300">
            <div class="relative flex items-center justify-center">
                <!-- Subtle glow behind logo on hover -->
                <div class="absolute inset-0 rounded-full bg-[#10B981]/0 group-hover:bg-[#10B981]/20 blur-md transition-all duration-500"></div>
                <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Logo" class="relative h-8 w-8 object-contain">
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-xl font-bold tracking-tight text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-white group-hover:to-[#7d8590] transition-all duration-300">Vision<span style="color:#7d8590;">Lab</span></span>
                <span class="hidden md:inline-block font-mono text-[10px] uppercase tracking-widest text-[#10B981] ml-1 opacity-80 group-hover:opacity-100 transition-opacity">Kernel</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('about') }}" class="text-sm font-semibold text-[#8b949e] hover:text-white transition-colors duration-200 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">Product</a>
            <a href="{{ route('features') }}" class="text-sm font-semibold text-[#8b949e] hover:text-white transition-colors duration-200 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">Features</a>
            <a href="{{ route('docs') }}" class="text-sm font-semibold text-[#8b949e] hover:text-white transition-colors duration-200 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">Documentation</a>
            <a href="{{ route('contact') }}" class="text-sm font-semibold text-[#8b949e] hover:text-white transition-colors duration-200 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">Contact</a>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-5">
            @auth
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-1.5 text-sm font-semibold text-white transition-all duration-300 rounded-md border border-white/10 hover:border-white/30 bg-white/5 hover:bg-white/10 shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]">
                Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="hidden md:inline-block text-sm font-semibold text-[#8b949e] hover:text-white transition-colors duration-200">
                Sign in
            </a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2 text-sm font-bold text-white transition-all duration-300 rounded-md bg-[#238636] hover:bg-[#2ea043] border border-[rgba(240,246,252,0.1)] shadow-[inset_0_1px_0_rgba(255,255,255,0.1),0_0_15px_rgba(35,134,54,0.2)] hover:shadow-[inset_0_1px_0_rgba(255,255,255,0.1),0_0_20px_rgba(35,134,54,0.4)] transform hover:-translate-y-0.5">
                Get Started
            </a>
            @endauth
        </div>
    </div>
</nav>
