<header class="h-14 flex-shrink-0 flex items-center justify-between px-6 transition-colors duration-300 backdrop-blur-md"
        style="background:var(--vc-nav);border-bottom:1px solid var(--vc-border);">

    <div class="flex items-center gap-3">
        {{-- Mobile sidebar toggle --}}
        <button onclick="document.getElementById('dash-sidebar').classList.toggle('-translate-x-full')" aria-label="Open mobile menu"
                class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                style="color:var(--vc-text-secondary);border:1px solid var(--vc-border);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-sm font-bold" style="color:var(--vc-text);">@yield('page-title', 'Dashboard')</h1>
    </div>

    <div class="flex items-center gap-2">
        {{-- Search --}}
        <div class="hidden md:flex items-center relative">
            <svg class="absolute left-3 w-3.5 h-3.5" style="color:var(--vc-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search..." class="pl-9 pr-3 py-1.5 rounded-lg text-xs w-52 transition-all duration-200 focus:w-60 focus:outline-none focus:ring-1"
                   style="background:var(--vc-elevated);border:1px solid var(--vc-border);color:var(--vc-text);--tw-ring-color:var(--vc-accent);">
        </div>

        {{-- Theme toggle --}}
        <button onclick="window.themeManager.toggle()" aria-label="Toggle Theme" class="theme-toggle" title="Toggle theme">
            <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        </button>

        {{-- Notification bell --}}
        @if(Auth::user()->isStudent())
        <button aria-label="Notifications" class="theme-toggle relative" title="Notifications">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span id="notif-badge" class="absolute -top-0.5 -right-0.5 hidden items-center justify-center w-4 h-4 rounded-full text-white text-[9px] font-black" style="background:var(--vc-accent);"><span id="notif-count">0</span></span>
        </button>
        @endif

        {{-- Role badge --}}
        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
              style="
              @if(Auth::user()->isAdmin()) color:#EF4444;background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.15);
              @elseif(Auth::user()->isInstructor()) color:var(--vc-accent);background:rgba(240,80,0,0.08);border-color:rgba(240,80,0,0.15);
              @else color:#16A34A;background:rgba(22,163,74,0.08);border-color:rgba(22,163,74,0.15);
              @endif
              ">
            {{ strtoupper(Auth::user()->role) }}
        </span>
    </div>
</header>
