<nav id="mainNav" class="fixed inset-x-0 top-0 z-50 transition-all duration-500 bg-[#040d21]/90 backdrop-blur-xl border-b border-white/5">
    <!-- Top thin gradient line -->
    <div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-[#10B981]/50 to-transparent"></div>

    <div class="mx-auto flex h-16 max-w-[1400px] items-center justify-between px-6 lg:px-8">
        <!-- Logo Section -->
        <a href="{{ route('home') }}" class="group flex items-center gap-3 transition-transform hover:opacity-90">
            <div class="relative flex items-center justify-center">
                <img src="{{ asset('icons/logo.svg') }}" alt="VisionLab Logo" class="relative h-8 w-8 object-contain">
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-xl font-bold tracking-tight text-white font-['Inter']">Vision<span style="color:#7d8590;">Lab</span></span>
                <span class="hidden md:inline-block font-mono text-[10px] uppercase tracking-widest text-[#10B981] ml-1">Kernel</span>
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
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-1.5 text-sm font-semibold text-white transition-all rounded-md border border-white/10 hover:border-white/30 bg-white/5 hover:bg-white/10">
                Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="hidden md:inline-block text-sm font-medium text-white/70 transition-colors hover:text-white">
                Sign in
            </a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-1.5 text-sm font-bold text-white transition-all rounded-md bg-[#238636] hover:bg-[#2ea043] border border-[rgba(240,246,252,0.1)]">
                Get Started
            </a>
            @endauth
        </div>
    </div>
</nav>
