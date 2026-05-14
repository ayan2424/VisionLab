<header class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 transition-colors duration-300 border-b backdrop-blur-md"
        style="background:var(--vc-nav); border-color:var(--vc-border);">
    
    <!-- Mobile Hamburger & Breadcrumbs/Title -->
    <div class="flex items-center gap-4">
        <!-- Mobile menu button (hidden on md and up) -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg transition-colors" style="color:var(--vc-text-secondary);">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        
        <!-- Page Title injected via slot or prop -->
        <h1 class="text-xl font-bold font-display tracking-tight" style="color:var(--vc-text);">
            {{ $title ?? 'Dashboard' }}
        </h1>
    </div>

    <!-- Right Side: Search, Theme Toggle, Notifications -->
    <div class="flex items-center gap-3">
        <!-- Search Input (Hidden on mobile) -->
        <div class="hidden md:block relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search..." 
                   class="pl-9 pr-4 py-1.5 text-sm rounded-full w-64 transition-all duration-200 focus:w-72 focus:outline-none"
                   style="background:var(--vc-elevated);border:1px solid var(--vc-border);color:var(--vc-text);">
        </div>

        <!-- Theme Toggle Button -->
        <button onclick="window.themeManager.toggle()" class="p-2 rounded-full transition-colors hover:bg-vc-elevated" style="color:var(--vc-text-secondary);" title="Toggle Theme">
            <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        <!-- Notification Bell -->
        @if(Auth::check() && Auth::user()->isStudent())
        <div x-data="notifBell()" class="relative">
            <button @click="toggle()" class="p-2 rounded-full transition-colors relative hover:bg-vc-elevated" style="color:var(--vc-text-secondary);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span id="notif-badge" class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 rounded-full text-white text-[9px] font-black hidden" style="background:var(--vc-accent);">
                    <span id="notif-count">0</span>
                </span>
            </button>
            <!-- Dropdown placeholder (logic already in app.js or navigation.blade.php) -->
        </div>
        @endif

        <!-- Logout Button (Icon only for sleekness) -->
        @if(Auth::check())
        <form method="POST" action="{{ route('logout') }}" class="ml-2">
            @csrf
            <button type="submit" class="p-2 rounded-full transition-colors hover:bg-red-500/10 text-red-500" title="Sign Out">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
        @endif
    </div>
</header>
