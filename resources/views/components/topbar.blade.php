<header class="h-16 flex-shrink-0 flex items-center justify-between px-8 rounded-full shadow-2xl transition-colors duration-300 z-30 relative"
        style="background:var(--vc-surface); border:1px solid var(--vc-border);">

    <div class="flex items-center gap-3">
        {{-- Mobile sidebar toggle --}}
        <button onclick="document.getElementById('dash-sidebar').classList.toggle('-translate-x-full')"
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

        
        

        {{-- Notification bell --}}
        @if(Auth::user()->isStudent())
        
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


