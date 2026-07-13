<nav id="mainNav" class="fixed inset-x-0 top-0 z-50 transition-all duration-500 bg-[#0A0A0F]/80 backdrop-blur-xl border-b border-white/5">
    <!-- Top thin gradient line -->
    <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-[#00F3FF]/50 to-transparent"></div>

    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6 lg:px-8">
        <!-- Logo Section -->
        <a href="{{ route('home') }}" class="group flex items-center gap-3 transition-transform hover:opacity-90">
            <div class="relative flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-[#00F3FF]/20 blur-lg transition-opacity group-hover:bg-[#00F3FF]/40"></div>
                <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Logo" class="relative h-8 w-8 object-contain">
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-xl font-bold tracking-tight text-white font-['Geist']">Vision<span style="color:#B026FF;">Lab</span></span>
                <span class="hidden md:inline-block font-mono text-[10px] uppercase tracking-widest text-[#00F3FF] ml-1">Kernel</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-6">
            <a href="{{ route('about') }}" class="text-sm font-medium text-white/70 transition-colors hover:text-white">Product</a>
            <a href="{{ route('features') }}" class="text-sm font-medium text-white/70 transition-colors hover:text-white">Features</a>
            <a href="{{ route('docs') }}" class="text-sm font-medium text-white/70 transition-colors hover:text-white">Documentation</a>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-4">
            @auth
            <a href="{{ route('dashboard') }}" class="relative inline-flex items-center justify-center px-5 py-2 text-sm font-semibold text-white transition-all rounded-full overflow-hidden group border border-white/10 hover:border-[#00F3FF]/50 bg-white/5 hover:bg-white/10">
                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-[#00F3FF]/0 via-[#00F3FF]/10 to-[#00F3FF]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></span>
                <span class="relative">Dashboard</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="hidden md:inline-block text-sm font-medium text-white/70 transition-colors hover:text-white">
                Sign in
            </a>
            <a href="{{ route('register') }}" class="relative inline-flex items-center justify-center px-5 py-2 text-sm font-bold text-[#0A0A0F] transition-all rounded-full overflow-hidden group bg-[#00F3FF] hover:bg-[#00F3FF]/90 shadow-[0_0_20px_rgba(0,243,255,0.4)] hover:shadow-[0_0_30px_rgba(0,243,255,0.6)]">
                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/30 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></span>
                <span class="relative">Get Started</span>
            </a>
            @endauth
        </div>
    </div>
</nav>
